<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminSettings extends MY_Controller {

	public function index()
	{
		$this->requireAdmin();

		$formError = NULL;
		$formSettings = $this->settings;

		if ($this->input->method() === 'post')
		{
			$this->form_validation->set_rules('store_name', 'Nama toko', 'trim|required|max_length[100]');
			$this->form_validation->set_rules('store_tagline', 'Tagline', 'trim|max_length[150]');
			$this->form_validation->set_rules('store_description', 'Deskripsi', 'trim|max_length[2000]');
			$this->form_validation->set_rules('store_address', 'Alamat', 'trim|max_length[255]');
			$this->form_validation->set_rules('store_phone', 'Telepon', 'trim|max_length[30]');
			$this->form_validation->set_rules('store_email', 'Email', 'trim|max_length[150]');
			$this->form_validation->set_rules('store_hours', 'Jam buka', 'trim|max_length[100]');
			$this->form_validation->set_rules('fine_per_day', 'Denda per hari', 'trim|required|integer|greater_than_equal_to[0]');
			$this->form_validation->set_rules('min_loan_days', 'Durasi minimum', 'trim|required|integer|greater_than_equal_to[3]|less_than_equal_to[7]');
			$this->form_validation->set_rules('max_loan_days', 'Durasi maksimum', 'trim|required|integer|greater_than_equal_to[3]|less_than_equal_to[7]');
			$this->form_validation->set_rules('pickup_deadline_days', 'Batas pengambilan', 'trim|required|integer|greater_than_equal_to[0]|less_than_equal_to[7]');

			$email = trim((string) $this->input->post('store_email'));

			if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				$formError = 'Format email toko tidak valid.';
			}

			$minDays = (int) $this->input->post('min_loan_days');
			$maxDays = (int) $this->input->post('max_loan_days');

			if ($formError === NULL && $minDays > $maxDays)
			{
				$formError = 'Durasi minimum tidak boleh lebih besar dari durasi maksimum.';
			}

			if ($this->form_validation->run() && $formError === NULL)
			{
				$this->SettingModel->setMany(array(
					'store_name'          => trim((string) $this->input->post('store_name')),
					'store_tagline'       => trim((string) $this->input->post('store_tagline')),
					'store_description'   => trim((string) $this->input->post('store_description')),
					'store_address'       => trim((string) $this->input->post('store_address')),
					'store_phone'         => trim((string) $this->input->post('store_phone')),
					'store_email'         => $email,
					'store_hours'         => trim((string) $this->input->post('store_hours')),
					'fine_per_day'        => (string) max(0, (int) $this->input->post('fine_per_day')),
					'min_loan_days'       => (string) $minDays,
					'max_loan_days'       => (string) $maxDays,
					'pickup_deadline_days'=> (string) max(0, (int) $this->input->post('pickup_deadline_days')),
				));

				$this->session->set_flashdata('success', 'Pengaturan toko disimpan.');
				redirect('admin/pengaturan');
			}

			$formSettings = array(
				'store_name'          => (string) $this->input->post('store_name'),
				'store_tagline'       => (string) $this->input->post('store_tagline'),
				'store_description'   => (string) $this->input->post('store_description'),
				'store_address'       => (string) $this->input->post('store_address'),
				'store_phone'         => (string) $this->input->post('store_phone'),
				'store_email'         => $email,
				'store_hours'         => (string) $this->input->post('store_hours'),
				'fine_per_day'        => (string) $this->input->post('fine_per_day'),
				'min_loan_days'       => (string) $this->input->post('min_loan_days'),
				'max_loan_days'       => (string) $this->input->post('max_loan_days'),
				'pickup_deadline_days'=> (string) $this->input->post('pickup_deadline_days'),
			);
		}

		$data['page_title'] = 'Pengaturan - Admin '.$this->settings['store_name'];
		$data['form_settings'] = $formSettings;
		$data['form_error'] = $formError;

		$this->renderAdmin('settings', $data);
	}
}
