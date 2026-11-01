<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoryModel extends CI_Model {

	/**
	 * Semua kategori beserta jumlah buku (termasuk buku nonaktif).
	 */
	public function getAll()
	{
		$this->db->select('categories.*, (SELECT COUNT(*) FROM books b WHERE b.category_id = categories.id) AS book_count', FALSE);
		$this->db->from('categories');

		return $this->db->order_by('categories.name', 'ASC')->get()->result_array();
	}

	public function getById($id)
	{
		return $this->db->where('id', (int) $id)->get('categories')->row_array();
	}

	public function getBySlug($slug)
	{
		return $this->db->where('slug', $slug)->get('categories')->row_array();
	}

	public function countBooks($id)
	{
		return $this->db->where('category_id', (int) $id)->count_all_results('books');
	}

	public function slugExists($slug, $exceptId = NULL)
	{
		$this->db->where('slug', $slug);

		if ($exceptId !== NULL)
		{
			$this->db->where('id !=', (int) $exceptId);
		}

		return $this->db->count_all_results('categories') > 0;
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
		$this->db->insert('categories', array(
			'name'        => $data['name'],
			'slug'        => $data['slug'],
			'description' => $data['description'],
		));

		return (int) $this->db->insert_id();
	}

	public function update($id, array $data)
	{
		$this->db->where('id', (int) $id)->update('categories', array(
			'name'        => $data['name'],
			'slug'        => $data['slug'],
			'description' => $data['description'],
		));
	}

	public function delete($id)
	{
		$this->db->where('id', (int) $id)->delete('categories');
	}
}
