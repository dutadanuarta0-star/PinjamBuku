<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminAuth extends MY_Controller {

	public function login()
	{
		if ($this->currentAdmin)
		{
			redirect('admin');
		}

		$this->load->model(array('AdminModel', 'RateLimitModel'));

		if ($this->input->method() === 'post')
		{
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[150]');
			$this->form_validation->set_rules('password', 'Password', 'required');

			if ($this->form_validation->run())
			{
				$email = strtolower(trim((string) $this->input->post('email')));
				$key = $this->input->ip_address().'|'.$email;

				if ( ! $this->RateLimitModel->attempt('admin_login', $key, 5, 900))
				{
					$this->session->set_flashdata('error', 'Terlalu banyak percobaan. Coba lagi dalam 15 menit.');
					redirect('admin/masuk');
				}

				$admin = $this->AdminModel->findByEmail($email);

				if ($admin && password_verify((string) $this->input->post('password'), $admin['password_hash']))
				{
					$this->RateLimitModel->clear('admin_login', $key);

					$this->session->sess_regenerate(TRUE);
					$this->session->set_userdata('admin', array(
						'id'    => (int) $admin['id'],
						'name'  => $admin['name'],
						'email' => $admin['email'],
					));
					$this->session->set_flashdata('success', 'Selamat datang, '.$admin['name'].'.');

					redirect('admin');
				}

				$this->session->set_flashdata('error', 'Email atau password admin salah.');
				redirect('admin/masuk');
			}
		}

		$data['page_title'] = 'Masuk Admin - '.$this->settings['store_name'];
		$data['settings'] = $this->settings;
		$this->load->view('admin/login', $data);
	}

	public function logout()
	{
		$this->requirePost();

		$this->session->unset_userdata('admin');
		$this->session->set_flashdata('success', 'Admin sudah keluar.');
		redirect('admin/masuk');
	}
}
