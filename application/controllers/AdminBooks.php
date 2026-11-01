<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminBooks extends MY_Controller {

	public function index()
	{
		$this->requireAdmin();
		$this->load->model(array('BookModel', 'CategoryModel'));

		$filters = array(
			'q'        => trim((string) $this->input->get('q')),
			'category' => trim((string) $this->input->get('kategori')),
			'status'   => trim((string) $this->input->get('status')),
		);

		$perPage = 10;
		$page = max(1, (int) $this->input->get('page'));
		$total = $this->BookModel->countAdmin($filters);

		if ($total > 0 && ($page - 1) * $perPage >= $total)
		{
			$page = 1;
		}

		$this->load->library('pagination');

		$this->pagination->initialize(pagination_config(site_url('admin/buku'), $total, $perPage));

		$data['page_title'] = 'Buku - Admin '.$this->settings['store_name'];
		$data['books'] = $this->BookModel->getAdminList($filters, $perPage, ($page - 1) * $perPage);
		$data['categories'] = $this->CategoryModel->getAll();
		$data['filters'] = $filters;
		$data['total'] = $total;
		$data['pagination'] = $this->pagination->create_links();

		$this->renderAdmin('books/index', $data);
	}

	public function create()
	{
		$this->requireAdmin();
		$this->load->model(array('BookModel', 'CategoryModel', 'VariantModel'));

		$this->_handleForm(NULL);
	}

	public function edit($id)
	{
		$this->requireAdmin();
		$this->load->model(array('BookModel', 'CategoryModel', 'VariantModel'));

		$book = $this->BookModel->getById($id);

		if ( ! $book)
		{
			show_404();
		}

		$this->_handleForm($book);
	}

	public function delete($id)
	{
		$this->requireAdmin();
		$this->requirePost();
		$this->load->model('BookModel');

		$book = $this->BookModel->getById($id);

		if ( ! $book)
		{
			show_404();
		}

		$this->BookModel->setActive($id, 0);
		$this->session->set_flashdata('success', 'Buku "'.$book['title'].'" dinonaktifkan. Aktifkan kembali lewat halaman ubah.');
		redirect('admin/buku');
	}

	private function _handleForm($book)
	{
		$isEdit = $book !== NULL;
		$existingVariants = $isEdit ? $this->VariantModel->getByBook($book['id'], FALSE) : array();
		$existingIds = array_map('intval', array_column($existingVariants, 'id'));

		$formError = NULL;

		if ($this->input->method() === 'post')
		{
			$this->form_validation->set_rules('category_id', 'Kategori', 'trim|required|integer');
			$this->form_validation->set_rules('title', 'Judul', 'trim|required|max_length[200]');
			$this->form_validation->set_rules('author', 'Penulis', 'trim|required|max_length[150]');
			$this->form_validation->set_rules('publisher', 'Penerbit', 'trim|max_length[150]');
			$this->form_validation->set_rules('publish_year', 'Tahun terbit', 'trim|integer|greater_than[0]|less_than[2100]');
			$this->form_validation->set_rules('isbn', 'ISBN', 'trim|max_length[30]');
			$this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[5000]');
			$this->form_validation->set_rules('cover_url', 'URL cover', 'trim|valid_url|max_length[500]');
			$this->form_validation->set_rules('is_active', 'Status', 'trim|required|in_list[0,1]');

			$parsed = $this->_postedVariants($existingIds, $isEdit ? $book['id'] : NULL);
			$variants = $parsed['rows'];
			$formError = $parsed['error'];

			if ( ! $this->form_validation->run())
			{
				$formError = $formError ?: NULL;
			}
			elseif ($formError === NULL && ! is_valid_cover_url($this->input->post('cover_url')))
			{
				$formError = 'URL cover harus berupa URL lengkap yang diawali http:// atau https://.';
			}
			elseif ($formError === NULL && ! $this->CategoryModel->getById((int) $this->input->post('category_id')))
			{
				$formError = 'Kategori yang dipilih tidak ditemukan.';
			}
			elseif ($formError === NULL)
			{
				$title = trim((string) $this->input->post('title'));
				$slug = $isEdit ? $book['slug'] : $this->BookModel->uniqueSlug(url_title($title, 'dash', TRUE) ?: 'buku');

				$payload = array(
					'category_id'  => (int) $this->input->post('category_id'),
					'title'        => $title,
					'slug'         => $slug,
					'author'       => trim((string) $this->input->post('author')),
					'publisher'    => trim((string) $this->input->post('publisher')),
					'publish_year' => $this->input->post('publish_year') !== '' ? (int) $this->input->post('publish_year') : NULL,
					'isbn'         => trim((string) $this->input->post('isbn')),
					'description'  => trim((string) $this->input->post('description')),
					'cover_url'    => trim((string) $this->input->post('cover_url')) ?: NULL,
					'is_active'    => (int) $this->input->post('is_active'),
				);

				$this->db->trans_begin();

				$bookId = $isEdit ? (int) $book['id'] : $this->BookModel->create($payload);

				if ($isEdit)
				{
					$this->BookModel->update($bookId, $payload);
				}

				foreach ($variants as $variant)
				{
					if ($this->VariantModel->nameExists($bookId, $variant['name'], $variant['id'] > 0 ? $variant['id'] : NULL))
					{
						$formError = 'Nama varian "'.$variant['name'].'" sudah dipakai.';
						break;
					}

					if ($variant['id'] > 0)
					{
						if ( ! $variant['is_active'] && $this->VariantModel->outstandingCount($variant['id']) > 0)
						{
							$formError = 'Varian "'.$variant['name'].'" tidak bisa dinonaktifkan karena masih dipinjam/direservasi.';
							break;
						}

						if ( ! $this->VariantModel->update($variant['id'], $variant['name'], $variant['stock_total'], $variant['is_active']))
						{
							$formError = 'Stok total varian "'.$variant['name'].'" tidak boleh lebih kecil dari jumlah yang sedang dipinjam/direservasi.';
							break;
						}
					}
					else
					{
						$this->VariantModel->create($bookId, $variant['name'], $variant['stock_total'], $variant['is_active']);
					}
				}

				if ($formError === NULL && $this->db->trans_status() !== FALSE)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('success', $isEdit ? 'Buku diperbarui.' : 'Buku ditambahkan.');
					redirect('admin/buku');
				}

				$this->db->trans_rollback();
			}

			$book = array(
				'id'           => $isEdit ? $book['id'] : NULL,
				'category_id'  => (int) $this->input->post('category_id'),
				'title'        => (string) $this->input->post('title'),
				'slug'         => $isEdit ? $book['slug'] : '',
				'author'       => (string) $this->input->post('author'),
				'publisher'    => (string) $this->input->post('publisher'),
				'publish_year' => (string) $this->input->post('publish_year'),
				'isbn'         => (string) $this->input->post('isbn'),
				'description'  => (string) $this->input->post('description'),
				'cover_url'    => (string) $this->input->post('cover_url'),
				'is_active'    => (int) $this->input->post('is_active'),
			);
		}
		elseif ( ! $isEdit)
		{
			$book = array(
				'id'           => NULL,
				'category_id'  => '',
				'title'        => '',
				'slug'         => '',
				'author'       => '',
				'publisher'    => '',
				'publish_year' => '',
				'isbn'         => '',
				'description'  => '',
				'cover_url'    => '',
				'is_active'    => 1,
			);
			$variants = array(
				array('id' => 0, 'name' => 'Paperback', 'stock_total' => 1, 'is_active' => 1),
			);
		}

		$data['page_title'] = ($isEdit ? 'Ubah' : 'Tambah').' Buku - Admin '.$this->settings['store_name'];
		$data['book'] = $book;
		$data['variants'] = isset($variants) ? $variants : $existingVariants;
		$data['categories'] = $this->CategoryModel->getAll();
		$data['is_edit'] = $isEdit;
		$data['form_error'] = $formError;

		$this->renderAdmin('books/form', $data);
	}

	/**
	 * Baca baris varian dari form. Baris baru yang kosong diabaikan.
	 */
	private function _postedVariants(array $existingIds, $bookId)
	{
		$ids = $this->input->post('variant_id');
		$names = $this->input->post('variant_name');
		$totals = $this->input->post('variant_total');
		$statuses = $this->input->post('variant_status');

		if ( ! is_array($names) OR ! is_array($totals) OR ! is_array($statuses))
		{
			return array('rows' => array(), 'error' => 'Minimal satu varian harus diisi.');
		}

		$rows = array();
		$seen = array();

		foreach ($names as $i => $name)
		{
			$id = isset($ids[$i]) ? (int) $ids[$i] : 0;
			$name = trim((string) $name);
			$totalRaw = isset($totals[$i]) ? trim((string) $totals[$i]) : '';
			$active = isset($statuses[$i]) ? ((int) $statuses[$i] === 1) : TRUE;

			if ($id === 0 && $name === '' && $totalRaw === '')
			{
				continue;
			}

			if ($name === '')
			{
				return array('rows' => $rows, 'error' => 'Nama varian wajib diisi.');
			}

			if (mb_strlen($name) > 80)
			{
				return array('rows' => $rows, 'error' => 'Nama varian maksimal 80 karakter.');
			}

			if ($totalRaw === '' OR ! ctype_digit($totalRaw))
			{
				return array('rows' => $rows, 'error' => 'Stok total varian "'.$name.'" harus berupa angka 0 atau lebih.');
			}

			if ($id > 0 && ! in_array($id, $existingIds, TRUE))
			{
				return array('rows' => $rows, 'error' => 'Varian tidak valid untuk buku ini.');
			}

			$key = mb_strtolower($name);

			if (isset($seen[$key]))
			{
				return array('rows' => $rows, 'error' => 'Nama varian "'.$name.'" dipakai lebih dari sekali.');
			}

			$seen[$key] = TRUE;
			$rows[] = array(
				'id'          => $id,
				'name'        => $name,
				'stock_total' => (int) $totalRaw,
				'is_active'   => $active ? 1 : 0,
			);
		}

		if (empty($rows))
		{
			return array('rows' => $rows, 'error' => 'Minimal satu varian harus diisi.');
		}

		return array('rows' => $rows, 'error' => NULL);
	}
}
