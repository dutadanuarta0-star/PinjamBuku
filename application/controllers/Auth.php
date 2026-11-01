<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {

	public function login()
	{
		if ($this->currentUser)
		{
			redirect('');
		}

		$this->load->model(array('UserModel', 'RateLimitModel'));

		if ($this->input->method() === 'post')
		{
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[150]');
			$this->form_validation->set_rules('password', 'Password', 'required');

			if ($this->form_validation->run())
			{
				$email = strtolower(trim((string) $this->input->post('email')));
				$key = $this->input->ip_address().'|'.$email;

				if ( ! $this->RateLimitModel->attempt('login', $key, 5, 900))
				{
					$this->session->set_flashdata('error', 'Terlalu banyak percobaan masuk. Coba lagi dalam 15 menit.');
					redirect('masuk');
				}

				$user = $this->UserModel->findByEmail($email);

				if ($user && password_verify((string) $this->input->post('password'), $user['password_hash']))
				{
					$this->RateLimitModel->clear('login', $key);
					$this->UserModel->touchLogin($user['id']);

					$this->session->sess_regenerate(TRUE);
					$this->session->set_userdata('user', array(
						'id'    => (int) $user['id'],
						'name'  => $user['name'],
						'email' => $user['email'],
					));
					$this->session->set_flashdata('success', 'Selamat datang kembali, '.$user['name'].'.');

					$this->_redirectAfterLogin();
				}

				$this->session->set_flashdata('error', 'Email atau password salah.');
				redirect('masuk');
			}
		}

		$data['page_title'] = 'Masuk - '.$this->settings['store_name'];
		$this->renderPublic('auth_login', $data);
	}

	public function register()
	{
		if ($this->currentUser)
		{
			redirect('');
		}

		$this->load->model(array('UserModel', 'RateLimitModel'));

		if ($this->input->method() === 'post')
		{
			$this->form_validation->set_rules('name', 'Nama', 'trim|required|min_length[2]|max_length[100]');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[150]');
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[100]');
			$this->form_validation->set_rules('password_confirm', 'Konfirmasi password', 'required|matches[password]');

			if ($this->form_validation->run())
			{
				$email = strtolower(trim((string) $this->input->post('email')));
				$key = $this->input->ip_address().'|'.$email;

				if ( ! $this->RateLimitModel->attempt('register', $key, 5, 900))
				{
					$this->session->set_flashdata('error', 'Terlalu banyak percobaan pendaftaran. Coba lagi nanti.');
					redirect('daftar');
				}

				if ($this->UserModel->emailExists($email))
				{
					$this->session->set_flashdata('error', 'Email sudah terdaftar. Silakan masuk.');
					redirect('daftar');
				}

				$userId = $this->UserModel->create(array(
					'name'          => trim((string) $this->input->post('name')),
					'email'         => $email,
					'password_hash' => password_hash((string) $this->input->post('password'), PASSWORD_DEFAULT),
				));

				if ( ! $userId)
				{
					$this->session->set_flashdata('error', 'Pendaftaran gagal. Silakan coba lagi.');
					redirect('daftar');
				}

				$this->UserModel->touchLogin($userId);
				$this->RateLimitModel->clear('register', $key);

				$this->session->sess_regenerate(TRUE);
				$this->session->set_userdata('user', array(
					'id'    => $userId,
					'name'  => trim((string) $this->input->post('name')),
					'email' => $email,
				));
				$this->session->set_flashdata('success', 'Akun berhasil dibuat. Selamat meminjam!');

				redirect('');
			}
		}

		$data['page_title'] = 'Daftar - '.$this->settings['store_name'];
		$this->renderPublic('auth_register', $data);
	}

	public function logout()
	{
		if ($this->input->method() !== 'post')
		{
			redirect('');
		}

		$this->session->unset_userdata('user');
		$this->session->set_flashdata('success', 'Kamu sudah keluar.');

		redirect('');
	}

	/**
	 * Kembali ke halaman yang dituju sebelum login, hanya untuk URL internal.
	 */
	private function _redirectAfterLogin()
	{
		$target = $this->session->userdata('redirect_after_login');
		$this->session->unset_userdata('redirect_after_login');

		if ( ! empty($target) && strpos($target, base_url()) === 0)
		{
			redirect($target);
		}

		redirect('');
	}
}
