<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminCategories extends MY_Controller {

	public function index()
	{
		$this->requireAdmin();
		$this->load->model('CategoryModel');

		$data['page_title'] = 'Kategori - Admin '.$this->settings['store_name'];
		$data['categories'] = $this->CategoryModel->getAll();

		$this->renderAdmin('categories/index', $data);
	}

	public function create()
	{
		$this->requireAdmin();
		$this->load->model('CategoryModel');

		$this->_handleForm(NULL);
	}

	public function edit($id)
	{
		$this->requireAdmin();
		$this->load->model('CategoryModel');

		$category = $this->CategoryModel->getById($id);

		if ( ! $category)
		{
			show_404();
		}

		$this->_handleForm($category);
	}

	public function delete($id)
	{
		$this->requireAdmin();
		$this->requirePost();
		$this->load->model('CategoryModel');

		$category = $this->CategoryModel->getById($id);

		if ( ! $category)
		{
			show_404();
		}

		if ($this->CategoryModel->countBooks($id) > 0)
		{
			$this->session->set_flashdata('error', 'Kategori tidak bisa dihapus karena masih memiliki buku.');
			redirect('admin/kategori');
		}

		$this->CategoryModel->delete($id);
		$this->session->set_flashdata('success', 'Kategori "'.$category['name'].'" dihapus.');
		redirect('admin/kategori');
	}

	private function _handleForm($category)
	{
		$isEdit = $category !== NULL;

		if ($this->input->method() === 'post')
		{
			$this->form_validation->set_rules('name', 'Nama kategori', 'trim|required|min_length[2]|max_length[100]');
			$this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[1000]');

			if ($this->form_validation->run())
			{
				$name = trim((string) $this->input->post('name'));
				$baseSlug = url_title($name, 'dash', TRUE);
				$baseSlug = $baseSlug ?: 'kategori';
				$slug = $this->CategoryModel->uniqueSlug($baseSlug, $isEdit ? $category['id'] : NULL);

				$payload = array(
					'name'        => $name,
					'slug'        => $slug,
					'description' => trim((string) $this->input->post('description')),
				);

				if ($isEdit)
				{
					$this->CategoryModel->update($category['id'], $payload);
					$this->session->set_flashdata('success', 'Kategori diperbarui.');
				}
				else
				{
					$this->CategoryModel->create($payload);
					$this->session->set_flashdata('success', 'Kategori ditambahkan.');
				}

				redirect('admin/kategori');
			}

			$category = array(
				'id'          => $isEdit ? $category['id'] : NULL,
				'name'        => (string) $this->input->post('name'),
				'slug'        => $isEdit ? $category['slug'] : '',
				'description' => (string) $this->input->post('description'),
			);
		}

		$data['page_title'] = ($isEdit ? 'Ubah' : 'Tambah').' Kategori - Admin '.$this->settings['store_name'];
		$data['category'] = $category ?: array(
			'id'          => NULL,
			'name'        => '',
			'slug'        => '',
			'description' => '',
		);
		$data['is_edit'] = $isEdit;

		$this->renderAdmin('categories/form', $data);
	}
}
