<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VariantModel extends CI_Model {

	public function getByBook($bookId, $onlyActive = TRUE)
	{
		$this->db->where('book_id', (int) $bookId);

		if ($onlyActive)
		{
			$this->db->where('is_active', 1);
		}

		return $this->db->order_by('name', 'ASC')->get('book_variants')->result_array();
	}

	public function getById($id)
	{
		return $this->db->where('id', (int) $id)->get('book_variants')->row_array();
	}

	/**
	 * Jumlah stok yang sedang ditahan reservasi/pinjaman aktif.
	 */
	public function outstandingCount($variantId)
	{
		return (int) $this->db->from('loan_items li')
			->join('loans l', 'l.id = li.loan_id')
			->where('li.variant_id', (int) $variantId)
			->where_in('l.status', array('pending', 'approved', 'borrowed'))
			->count_all_results();
	}

	public function nameExists($bookId, $name, $exceptId = NULL)
	{
		$this->db->where('book_id', (int) $bookId)->where('name', $name);

		if ($exceptId !== NULL)
		{
			$this->db->where('id !=', (int) $exceptId);
		}

		return $this->db->count_all_results('book_variants') > 0;
	}

	public function create($bookId, $name, $total, $active = TRUE)
	{
		$total = max(0, (int) $total);

		$this->db->insert('book_variants', array(
			'book_id'         => (int) $bookId,
			'name'            => $name,
			'stock_total'     => $total,
			'stock_available' => $total,
			'is_active'       => $active ? 1 : 0,
		));

		return (int) $this->db->insert_id();
	}

	/**
	 * Perbarui varian. Stok tersedia dihitung ulang dari total dikurangi stok yang ditahan.
	 */
	public function update($id, $name, $total, $active = TRUE)
	{
		$variant = $this->getById($id);

		if ( ! $variant)
		{
			return FALSE;
		}

		$outstanding = $this->outstandingCount($id);
		$total = (int) $total;

		if ($total < $outstanding)
		{
			return FALSE;
		}

		$this->db->where('id', (int) $id)->update('book_variants', array(
			'name'            => $name,
			'stock_total'     => $total,
			'stock_available' => $total - $outstanding,
			'is_active'       => $active ? 1 : 0,
		));

		if ( ! $active)
		{
			$this->db->where('variant_id', (int) $id)->delete('cart_items');
		}

		return TRUE;
	}

	public function setActive($id, $active)
	{
		if ( ! $active && $this->outstandingCount($id) > 0)
		{
			return FALSE;
		}

		$this->db->where('id', (int) $id)->update('book_variants', array('is_active' => $active ? 1 : 0));

		if ( ! $active)
		{
			$this->db->where('variant_id', (int) $id)->delete('cart_items');
		}

		return TRUE;
	}

	/**
	 * Kurangi stok secara atomik; gagal jika stok sudah habis.
	 */
	public function decrementStock($id)
	{
		$this->db->set('stock_available', 'stock_available - 1', FALSE)
			->where('id', (int) $id)
			->where('stock_available >', 0)
			->update('book_variants');

		return $this->db->affected_rows() === 1;
	}

	public function incrementStock($id)
	{
		$this->db->set('stock_available', 'LEAST(stock_available + 1, stock_total)', FALSE)
			->where('id', (int) $id)
			->update('book_variants');
	}

	public function sumAvailableByBook($bookId)
	{
		$row = $this->db->select('COALESCE(SUM(stock_available), 0) AS total', FALSE)
			->where('book_id', (int) $bookId)
			->where('is_active', 1)
			->get('book_variants')
			->row_array();

		return (int) $row['total'];
	}
}
