<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminDashboard extends MY_Controller {

	public function index()
	{
		$this->requireAdmin();
		$this->load->model('BookModel');

		$data['page_title'] = 'Dashboard - Admin '.$this->settings['store_name'];
		$data['stats'] = $this->AnalyticsModel->visitorStats();
		$data['top_books'] = $this->AnalyticsModel->topBooks(5, 30);
		$data['total_books'] = $this->BookModel->countActive();
		$data['pending_loans'] = $this->LoanModel->countByStatus('pending');
		$data['active_loans'] = $this->LoanModel->countActive();
		$data['overdue_items'] = $this->LoanModel->countOverdue();
		$data['recent_pending'] = $this->LoanModel->getRecentPending(5);

		$this->renderAdmin('dashboard', $data);
	}
}
