<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends MY_Controller {

	public function index()
	{
		$this->requireUser();

		$data['page_title'] = 'Keranjang - '.$this->settings['store_name'];
		$data['items'] = $this->CartModel->getItems((int) $this->currentUser['id']);

		$this->renderPublic('cart', $data);
	}

	public function addItem()
	{
		$this->requireUser();
		$this->requirePost();
		$this->load->model(array('BookModel', 'VariantModel'));

		$bookId = (int) $this->input->post('book_id');
		$variantId = (int) $this->input->post('variant_id');
		$duration = (int) $this->input->post('duration_days');
		$minDays = (int) $this->settings['min_loan_days'];
		$maxDays = (int) $this->settings['max_loan_days'];

		$book = $this->BookModel->getById($bookId);
		$variant = $this->VariantModel->getById($variantId);

		if ( ! $book OR ! $book['is_active'] OR ! $variant OR (int) $variant['book_id'] !== $bookId OR ! $variant['is_active'])
		{
			$this->session->set_flashdata('error', 'Buku atau varian tidak ditemukan.');
			redirect('katalog');
		}

		if ($duration < $minDays OR $duration > $maxDays)
		{
			$this->session->set_flashdata('error', 'Durasi pinjam harus antara '.$minDays.' dan '.$maxDays.' hari.');
			redirect('buku/'.$book['slug']);
		}

		if ((int) $variant['stock_available'] < 1)
		{
			$this->session->set_flashdata('error', 'Stok varian ini sedang habis.');
			redirect('buku/'.$book['slug']);
		}

		$this->CartModel->add((int) $this->currentUser['id'], $bookId, $variantId, $duration);
		$this->session->set_flashdata('success', 'Buku ditambahkan ke keranjang.');

		$this->_back('buku/'.$book['slug']);
	}

	public function updateItem()
	{
		$this->requireUser();
		$this->requirePost();

		$itemId = (int) $this->input->post('item_id');
		$duration = (int) $this->input->post('duration_days');
		$minDays = (int) $this->settings['min_loan_days'];
		$maxDays = (int) $this->settings['max_loan_days'];

		if ( ! $this->CartModel->getItem($itemId, (int) $this->currentUser['id']))
		{
			$this->session->set_flashdata('error', 'Item keranjang tidak ditemukan.');
			redirect('keranjang');
		}

		if ($duration < $minDays OR $duration > $maxDays)
		{
			$this->session->set_flashdata('error', 'Durasi pinjam harus antara '.$minDays.' dan '.$maxDays.' hari.');
			redirect('keranjang');
		}

		$this->CartModel->updateDuration($itemId, (int) $this->currentUser['id'], $duration);
		$this->session->set_flashdata('success', 'Durasi pinjam diperbarui.');

		redirect('keranjang');
	}

	public function removeItem()
	{
		$this->requireUser();
		$this->requirePost();

		$itemId = (int) $this->input->post('item_id');
		$this->CartModel->remove($itemId, (int) $this->currentUser['id']);
		$this->session->set_flashdata('success', 'Buku dihapus dari keranjang.');

		redirect('keranjang');
	}

	public function checkout()
	{
		$this->requireUser();
		$this->requirePost();
		$this->load->model('RateLimitModel');

		$userId = (int) $this->currentUser['id'];

		if ( ! $this->RateLimitModel->attempt('checkout', (string) $userId, 10, 60))
		{
			$this->session->set_flashdata('error', 'Terlalu banyak percobaan reservasi. Tunggu sebentar.');
			redirect('keranjang');
		}

		$items = $this->CartModel->getItems($userId);
		$loanId = $this->LoanModel->createFromCart($userId, $items, $this->settings);

		if ( ! $loanId)
		{
			$this->session->set_flashdata('error', $this->LoanModel->lastError ?: 'Reservasi gagal dibuat.');
			redirect('keranjang');
		}

		$loan = $this->LoanModel->getById($loanId);
		$this->session->set_flashdata('success', 'Reservasi '.$loan['code'].' berhasil dibuat. Tunggu konfirmasi admin.');
		redirect('peminjaman');
	}

	/**
	 * Kembali ke halaman asal hanya jika masih di situs ini.
	 */
	private function _back($fallback)
	{
		$referer = $this->input->server('HTTP_REFERER');

		if ( ! empty($referer) && strpos($referer, base_url()) === 0)
		{
			redirect($referer);
		}

		redirect($fallback);
	}
}
