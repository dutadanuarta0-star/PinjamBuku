<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookModel extends CI_Model {

	/**
	 * Daftar buku untuk katalog publik.
	 */
	public function getCatalog(array $filters = array(), $limit = NULL, $offset = 0)
	{
		$this->db->select('books.*, categories.name AS category_name, categories.slug AS category_slug,
			(SELECT COALESCE(SUM(v.stock_available), 0) FROM book_variants v WHERE v.book_id = books.id AND v.is_active = 1) AS stock_available,
			(SELECT COALESCE(SUM(v.stock_total), 0) FROM book_variants v WHERE v.book_id = books.id AND v.is_active = 1) AS stock_total,
			(SELECT COUNT(*) FROM book_variants v2 WHERE v2.book_id = books.id AND v2.is_active = 1) AS variant_count', FALSE);
		$this->db->from('books');
		$this->db->join('categories', 'categories.id = books.category_id');
		$this->db->where('books.is_active', 1);
		$this->_applyFilters($filters);

		if ($limit !== NULL)
		{
			$this->db->limit((int) $limit, (int) $offset);
		}

		return $this->db->order_by('books.created_at', 'DESC')->get()->result_array();
	}

	public function countCatalog(array $filters = array())
	{
		$this->db->from('books');
		$this->db->join('categories', 'categories.id = books.category_id');
		$this->db->where('books.is_active', 1);
		$this->_applyFilters($filters);

		return (int) $this->db->count_all_results();
	}

	/**
	 * Daftar buku untuk admin (termasuk nonaktif).
	 */
	public function getAdminList(array $filters = array(), $limit = NULL, $offset = 0)
	{
		$this->db->select('books.*, categories.name AS category_name,
			(SELECT COALESCE(SUM(v.stock_available), 0) FROM book_variants v WHERE v.book_id = books.id AND v.is_active = 1) AS stock_available,
			(SELECT COALESCE(SUM(v.stock_total), 0) FROM book_variants v WHERE v.book_id = books.id AND v.is_active = 1) AS stock_total,
			(SELECT COUNT(*) FROM book_variants v2 WHERE v2.book_id = books.id) AS variant_count', FALSE);
		$this->db->from('books');
		$this->db->join('categories', 'categories.id = books.category_id');
		$this->_applyFilters($filters);

		if ( ! empty($filters['status']))
		{
			$this->db->where('books.is_active', $filters['status'] === 'active' ? 1 : 0);
		}

		if ($limit !== NULL)
		{
			$this->db->limit((int) $limit, (int) $offset);
		}

		return $this->db->order_by('books.created_at', 'DESC')->get()->result_array();
	}

	public function countAdmin(array $filters = array())
	{
		$this->db->from('books');
		$this->db->join('categories', 'categories.id = books.category_id');
		$this->_applyFilters($filters);

		if ( ! empty($filters['status']))
		{
			$this->db->where('books.is_active', $filters['status'] === 'active' ? 1 : 0);
		}

		return (int) $this->db->count_all_results();
	}

	public function getBySlug($slug, $onlyActive = TRUE)
	{
		$this->db->select('books.*, categories.name AS category_name, categories.slug AS category_slug', FALSE);
		$this->db->from('books');
		$this->db->join('categories', 'categories.id = books.category_id');
		$this->db->where('books.slug', $slug);

		if ($onlyActive)
		{
			$this->db->where('books.is_active', 1);
		}

		return $this->db->get()->row_array();
	}

	public function getById($id)
	{
		return $this->db->where('id', (int) $id)->get('books')->row_array();
	}

	public function getRelated($categoryId, $excludeId, $limit = 4)
	{
		return $this->db->select('books.*, categories.name AS category_name, categories.slug AS category_slug,
			(SELECT COALESCE(SUM(v.stock_available), 0) FROM book_variants v WHERE v.book_id = books.id AND v.is_active = 1) AS stock_available,
			(SELECT COALESCE(SUM(v.stock_total), 0) FROM book_variants v WHERE v.book_id = books.id AND v.is_active = 1) AS stock_total,
			(SELECT COUNT(*) FROM book_variants v2 WHERE v2.book_id = books.id AND v2.is_active = 1) AS variant_count', FALSE)
			->from('books')
			->join('categories', 'categories.id = books.category_id')
			->where('books.category_id', (int) $categoryId)
			->where('books.id !=', (int) $excludeId)
			->where('books.is_active', 1)
			->order_by('books.created_at', 'DESC')
			->limit((int) $limit)
			->get()
			->result_array();
	}

	public function slugExists($slug, $exceptId = NULL)
	{
		$this->db->where('slug', $slug);

		if ($exceptId !== NULL)
		{
			$this->db->where('id !=', (int) $exceptId);
		}

		return $this->db->count_all_results('books') > 0;
	}

	public function uniqueSlug($base, $exceptId = NULL)
	{
		$slug = $base;
		$i = 1;

		while ($this->slugExists($slug, $exceptId))
		{
			$i++;
			$slug = $base.'-'.$i;
		}

		return $slug;
	}

	public function create(array $data)
	{
		$this->db->insert('books', array(
			'category_id' => (int) $data['category_id'],
			'title'       => $data['title'],
			'slug'        => $data['slug'],
			'author'      => $data['author'],
			'publisher'   => $data['publisher'],
			'publish_year'=> $data['publish_year'],
			'isbn'        => $data['isbn'],
			'description' => $data['description'],
			'cover_url'   => $data['cover_url'],
			'is_active'   => ! empty($data['is_active']) ? 1 : 0,
		));

		return (int) $this->db->insert_id();
	}

	public function update($id, array $data)
	{
		$this->db->where('id', (int) $id)->update('books', array(
			'category_id' => (int) $data['category_id'],
			'title'       => $data['title'],
			'slug'        => $data['slug'],
			'author'      => $data['author'],
			'publisher'   => $data['publisher'],
			'publish_year'=> $data['publish_year'],
			'isbn'        => $data['isbn'],
			'description' => $data['description'],
			'cover_url'   => $data['cover_url'],
			'is_active'   => ! empty($data['is_active']) ? 1 : 0,
		));
	}

	public function setActive($id, $active)
	{
		$this->db->where('id', (int) $id)->update('books', array(
			'is_active' => $active ? 1 : 0,
		));
	}

	public function countActive()
	{
		return $this->db->where('is_active', 1)->count_all_results('books');
	}

	private function _applyFilters(array $filters)
	{
		if ( ! empty($filters['q']))
		{
			$q = $filters['q'];
			$this->db->group_start()
				->like('books.title', $q)
				->or_like('books.author', $q)
				->or_like('books.isbn', $q)
				->group_end();
		}

		if ( ! empty($filters['category']))
		{
			$this->db->where('categories.slug', $filters['category']);
		}
	}
}
