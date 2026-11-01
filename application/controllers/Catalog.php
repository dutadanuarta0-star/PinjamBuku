<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Catalog extends MY_Controller {

	public function index()
	{
		$this->load->model(array('BookModel', 'CategoryModel'));

		$filters = array(
			'q'        => trim((string) $this->input->get('q')),
			'category' => trim((string) $this->input->get('kategori')),
		);

		$perPage = 9;
		$page = max(1, (int) $this->input->get('page'));
		$total = $this->BookModel->countCatalog($filters);

		if ($total > 0 && ($page - 1) * $perPage >= $total)
		{
			$page = 1;
		}

		$this->load->library('pagination');

		$this->pagination->initialize(pagination_config(site_url('katalog'), $total, $perPage));

		$this->trackPage();

		$data['page_title'] = 'Katalog Buku - '.$this->settings['store_name'];
		$data['books'] = $this->BookModel->getCatalog($filters, $perPage, ($page - 1) * $perPage);
		$data['categories'] = $this->CategoryModel->getAll();
		$data['filters'] = $filters;
		$data['total'] = $total;
		$data['pagination'] = $this->pagination->create_links();

		$this->renderPublic('catalog', $data);
	}

	public function detail($slug)
	{
		$this->load->model(array('BookModel', 'VariantModel'));

		$book = $this->BookModel->getBySlug($slug);

		if ( ! $book)
		{
			show_404();
		}

		$this->trackPage($book['id']);

		$data['page_title'] = $book['title'].' - '.$this->settings['store_name'];
		$data['book'] = $book;
		$data['variants'] = $this->VariantModel->getByBook($book['id']);
		$data['related'] = $this->BookModel->getRelated($book['category_id'], $book['id'], 4);

		$this->renderPublic('book_detail', $data);
	}
}
