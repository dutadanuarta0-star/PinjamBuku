<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AnalyticsModel extends CI_Model {

	/**
	 * Catat kunjungan halaman publik. Session di-hash agar tidak menyimpan ID mentah.
	 */
	public function track($path, $bookId = NULL)
	{
		$sessionId = session_id();

		if (empty($sessionId))
		{
			$sessionId = 'guest-'.$this->input->ip_address();
		}

		$this->db->insert('page_views', array(
			'session_id' => hash('sha256', $sessionId),
			'path'       => mb_substr($path, 0, 255),
			'book_id'    => $bookId ? (int) $bookId : NULL,
			'ip_hash'    => hash('sha256', $this->input->ip_address().'|pinjambuku'),
			'created_at' => date('Y-m-d H:i:s'),
		));
	}

	public function visitorStats()
	{
		$todayStart = date('Y-m-d 00:00:00');
		$weekStart = date('Y-m-d 00:00:00', strtotime('-6 days'));

		return array(
			'today'       => $this->_distinctVisitors($todayStart),
			'week'        => $this->_distinctVisitors($weekStart),
			'total_views' => (int) $this->db->count_all_results('page_views'),
			'total_today' => (int) $this->db->where('created_at >=', $todayStart)->count_all_results('page_views'),
		);
	}

	public function topBooks($limit = 5, $days = 30)
	{
		$start = date('Y-m-d 00:00:00', strtotime('-'.((int) $days - 1).' days'));

		return $this->db->select('books.id, books.title, books.slug, COUNT(page_views.id) AS views', FALSE)
			->from('page_views')
			->join('books', 'books.id = page_views.book_id')
			->where('page_views.book_id IS NOT NULL', NULL, FALSE)
			->where('page_views.created_at >=', $start)
			->group_by('books.id')
			->order_by('views', 'DESC', FALSE)
			->limit((int) $limit)
			->get()
			->result_array();
	}

	private function _distinctVisitors($from)
	{
		$row = $this->db->select('COUNT(DISTINCT session_id) AS total', FALSE)
			->where('created_at >=', $from)
			->get('page_views')
			->row_array();

		return (int) $row['total'];
	}
}
