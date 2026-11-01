<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminLoans extends MY_Controller {

	public function index()
	{
		$this->requireAdmin();

		$allowed = array('pending', 'approved', 'borrowed', 'returned', 'rejected', 'cancelled', 'expired');
		$status = trim((string) $this->input->get('status'));

		$filters = array(
			'q'      => trim((string) $this->input->get('q')),
			'status' => in_array($status, $allowed, TRUE) ? $status : '',
		);

		$perPage = 12;
		$page = max(1, (int) $this->input->get('page'));
		$total = $this->LoanModel->countAdmin($filters);

		if ($total > 0 && ($page - 1) * $perPage >= $total)
		{
			$page = 1;
		}

		$this->load->library('pagination');

		$this->pagination->initialize(pagination_config(site_url('admin/peminjaman'), $total, $perPage));

		$data['page_title'] = 'Peminjaman - Admin '.$this->settings['store_name'];
		$data['loans'] = $this->LoanModel->getAdminList($filters, $perPage, ($page - 1) * $perPage);
		$data['filters'] = $filters;
		$data['total'] = $total;
		$data['pagination'] = $this->pagination->create_links();
		$data['statuses'] = $allowed;

		$this->renderAdmin('loans/index', $data);
	}

	public function detail($id)
	{
		$this->requireAdmin();

		$loan = $this->LoanModel->getAdminDetail($id);

		if ( ! $loan)
		{
			show_404();
		}

		$finePerDay = (int) $this->settings['fine_per_day'];
		$loan['total_fine'] = 0;

		foreach ($loan['items'] as &$item)
		{
			if ($loan['status'] === 'borrowed' && ! empty($item['due_date']))
			{
				$item['late_days'] = days_late($item['due_date']);
				$item['fine_preview'] = $item['late_days'] * $finePerDay;
			}
			else
			{
				$item['late_days'] = ! empty($item['due_date']) ? days_late($item['due_date'], $item['returned_at']) : 0;
				$item['fine_preview'] = (int) $item['fine_amount'];
			}

			$loan['total_fine'] += (int) $item['fine_preview'];
		}
		unset($item);

		$data['page_title'] = 'Detail Peminjaman - Admin '.$this->settings['store_name'];
		$data['loan'] = $loan;
		$data['fine_per_day'] = $finePerDay;

		$this->renderAdmin('loans/detail', $data);
	}

	public function approve($id)
	{
		$this->requireAdmin();
		$this->requirePost();

		if ($this->LoanModel->approve($id, (int) $this->settings['pickup_deadline_days']))
		{
			$this->session->set_flashdata('success', 'Reservasi disetujui. Customer bisa mengambil buku.');
		}
		else
		{
			$this->session->set_flashdata('error', 'Reservasi tidak bisa disetujui.');
		}

		redirect('admin/peminjaman/'.(int) $id);
	}

	public function reject($id)
	{
		$this->requireAdmin();
		$this->requirePost();

		$reason = trim((string) $this->input->post('reason'));
		$reason = $reason === '' ? 'Ditolak oleh admin.' : mb_substr($reason, 0, 255);

		if ($this->LoanModel->reject($id, $reason))
		{
			$this->session->set_flashdata('success', 'Reservasi ditolak dan stok dikembalikan.');
		}
		else
		{
			$this->session->set_flashdata('error', 'Reservasi tidak bisa ditolak.');
		}

		redirect('admin/peminjaman/'.(int) $id);
	}

	public function pickup($id)
	{
		$this->requireAdmin();
		$this->requirePost();

		if ($this->LoanModel->pickup($id))
		{
			$this->session->set_flashdata('success', 'Buku ditandai sudah diambil. Tanggal jatuh tempo sudah dihitung.');
		}
		else
		{
			$this->session->set_flashdata('error', 'Buku tidak bisa ditandai diambil.');
		}

		redirect('admin/peminjaman/'.(int) $id);
	}

	public function returnBooks($id)
	{
		$this->requireAdmin();
		$this->requirePost();

		if ($this->LoanModel->returnBooks($id, (int) $this->settings['fine_per_day']))
		{
			$this->session->set_flashdata('success', 'Buku dikembalikan. Denda (jika ada) sudah dihitung.');
		}
		else
		{
			$this->session->set_flashdata('error', 'Buku tidak bisa ditandai dikembalikan.');
		}

		redirect('admin/peminjaman/'.(int) $id);
	}

	public function markFinePaid($id)
	{
		$this->requireAdmin();
		$this->requirePost();

		$this->LoanModel->markFinePaid($id);
		$this->session->set_flashdata('success', 'Denda ditandai lunas.');
		redirect('admin/peminjaman/'.(int) $id);
	}
}
