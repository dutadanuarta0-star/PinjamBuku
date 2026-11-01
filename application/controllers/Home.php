<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

	public function index()
	{
		$this->load->model(array('BookModel', 'CategoryModel'));

		$this->trackPage();

		$data['page_title'] = $this->settings['store_name'].' - Pinjam buku favoritmu';
		$data['books'] = $this->BookModel->getCatalog(array(), 8);
		$data['categories'] = $this->CategoryModel->getAll();

		$this->renderPublic('home', $data);
	}

	public function about()
	{
		$this->trackPage();

		$data['page_title'] = 'Tentang Kami - '.$this->settings['store_name'];

		$this->renderPublic('about', $data);
	}
}
