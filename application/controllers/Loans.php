<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loans extends MY_Controller {

	public function index()
	{
		$this->requireUser();

		$finePerDay = (int) $this->settings['fine_per_day'];

		$data['page_title'] = 'Peminjaman Saya - '.$this->settings['store_name'];
		$data['loans'] = $this->LoanModel->getForUser((int) $this->currentUser['id'], $finePerDay);
		$data['fine_per_day'] = $finePerDay;

		$this->renderPublic('loans', $data);
	}

	public function cancel($id)
	{
		$this->requireUser();
		$this->requirePost();

		if ($this->LoanModel->cancel((int) $id, (int) $this->currentUser['id']))
		{
			$this->session->set_flashdata('success', 'Reservasi dibatalkan dan stok dikembalikan.');
		}
		else
		{
			$this->session->set_flashdata('error', 'Reservasi tidak bisa dibatalkan.');
		}

		redirect('peminjaman');
	}
}
