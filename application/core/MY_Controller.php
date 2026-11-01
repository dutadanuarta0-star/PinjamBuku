<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base controller aplikasi.
 * Menyediakan pengaturan toko, data user/admin yang login, render layout,
 * guard autentikasi, dan pembersihan reservasi kedaluwarsa.
 */
class MY_Controller extends CI_Controller {

	protected $settings = array();
	protected $currentUser = NULL;
	protected $currentAdmin = NULL;
	protected $cartCount = 0;
	protected $userAlertOverdue = 0;
	protected $userAlertFine = 0;

	public function __construct()
	{
		date_default_timezone_set('Asia/Jakarta');

		parent::__construct();

		$this->load->database();
		$this->load->library('session');
		$this->load->helper(array('url', 'form', 'pinjambuku'));
		$this->load->model(array('SettingModel', 'CartModel', 'LoanModel', 'AnalyticsModel'));

		$this->settings = $this->SettingModel->getAll();
		$this->currentUser = $this->session->userdata('user');
		$this->currentAdmin = $this->session->userdata('admin');

		if ($this->currentUser)
		{
			$this->cartCount = $this->CartModel->count((int) $this->currentUser['id']);
		}

		// Tidak ada cron di XAMPP: reservasi yang lewat batas dibersihkan saat ada request.
		$this->LoanModel->expireStale();

		if ($this->currentUser)
		{
			$userId = (int) $this->currentUser['id'];
			$this->userAlertOverdue = $this->LoanModel->countUserOverdue($userId);
			$this->userAlertFine = $this->LoanModel->sumUserUnpaidFine($userId);
		}
	}

	/**
	 * Render halaman storefront dengan layout publik.
	 */
	protected function renderPublic($view, array $data = array())
	{
		$data['settings'] = $this->settings;
		$data['current_user'] = $this->currentUser;
		$data['cart_count'] = $this->cartCount;
		$data['user_alert_overdue'] = $this->userAlertOverdue;
		$data['user_alert_fine'] = $this->userAlertFine;
		$data['page_title'] = isset($data['page_title']) ? $data['page_title'] : $this->settings['store_name'];

		$this->load->view('layouts/public_header', $data);
		$this->load->view('public/'.$view, $data);
		$this->load->view('layouts/public_footer', $data);
	}

	/**
	 * Render halaman admin dengan layout admin.
	 */
	protected function renderAdmin($view, array $data = array())
	{
		$data['settings'] = $this->settings;
		$data['current_admin'] = $this->currentAdmin;
		$data['pending_count'] = $this->LoanModel->countByStatus('pending');
		$data['page_title'] = isset($data['page_title']) ? $data['page_title'] : 'Admin '.$this->settings['store_name'];

		$this->load->view('layouts/admin_header', $data);
		$this->load->view('admin/'.$view, $data);
		$this->load->view('layouts/admin_footer', $data);
	}

	/**
	 * Wajib login customer. Setelah login, user dikembalikan ke halaman asal.
	 */
	protected function requireUser($returnUrl = NULL)
	{
		if ($this->currentUser)
		{
			return;
		}

		if ($returnUrl === NULL)
		{
			$returnUrl = ($this->input->method() === 'post')
				? ($this->input->server('HTTP_REFERER') ?: site_url())
				: current_url();
		}

		$this->session->set_userdata('redirect_after_login', $returnUrl);
		$this->session->set_flashdata('error', 'Silakan masuk terlebih dahulu untuk melanjutkan.');
		redirect('masuk');
	}

	/**
	 * Wajib login admin.
	 */
	protected function requireAdmin()
	{
		if ($this->currentAdmin)
		{
			return;
		}

		$this->session->set_flashdata('error', 'Silakan masuk sebagai admin.');
		redirect('admin/masuk');
	}

	/**
	 * Tolak request selain POST (form selalu memakai CSRF token).
	 */
	protected function requirePost()
	{
		if ($this->input->method() !== 'post')
		{
			show_error('Metode tidak diizinkan.', 405);
		}
	}

	/**
	 * Catat kunjungan halaman publik untuk dashboard admin.
	 */
	protected function trackPage($bookId = NULL)
	{
		$this->AnalyticsModel->track(current_url(), $bookId);
	}
}
