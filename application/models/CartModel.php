<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CartModel extends CI_Model {

	public function count($userId)
	{
		return (int) $this->db->where('user_id', (int) $userId)->count_all_results('cart_items');
	}

	public function getItems($userId)
	{
		return $this->db->select('cart_items.*, books.title, books.slug AS book_slug, books.cover_url,
			books.is_active AS book_active, categories.name AS category_name,
			book_variants.name AS variant_name, book_variants.stock_available,
			book_variants.stock_total, book_variants.is_active AS variant_active')
			->from('cart_items')
			->join('books', 'books.id = cart_items.book_id')
			->join('categories', 'categories.id = books.category_id')
			->join('book_variants', 'book_variants.id = cart_items.variant_id')
			->where('cart_items.user_id', (int) $userId)
			->order_by('cart_items.created_at', 'ASC')
			->get()
			->result_array();
	}

	public function getItem($id, $userId)
	{
		return $this->db->where('id', (int) $id)
			->where('user_id', (int) $userId)
			->get('cart_items')
			->row_array();
	}

	public function add($userId, $bookId, $variantId, $durationDays)
	{
		$existing = $this->db->where('user_id', (int) $userId)
			->where('variant_id', (int) $variantId)
			->get('cart_items')
			->row_array();

		if ($existing)
		{
			$this->db->where('id', $existing['id'])->update('cart_items', array(
				'duration_days' => (int) $durationDays,
			));

			return (int) $existing['id'];
		}

		$this->db->insert('cart_items', array(
			'user_id'       => (int) $userId,
			'book_id'       => (int) $bookId,
			'variant_id'    => (int) $variantId,
			'duration_days' => (int) $durationDays,
		));

		return (int) $this->db->insert_id();
	}

	public function updateDuration($id, $userId, $durationDays)
	{
		$this->db->where('id', (int) $id)
			->where('user_id', (int) $userId)
			->update('cart_items', array('duration_days' => (int) $durationDays));

		return $this->db->affected_rows() >= 0;
	}

	public function remove($id, $userId)
	{
		$this->db->where('id', (int) $id)
			->where('user_id', (int) $userId)
			->delete('cart_items');
	}

	public function clear($userId)
	{
		$this->db->where('user_id', (int) $userId)->delete('cart_items');
	}
}
