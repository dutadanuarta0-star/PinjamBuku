<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoanModel extends CI_Model {

	public $lastError = NULL;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('VariantModel');
	}

	/**
	 * Reservasi/peminjaman milik satu customer, lengkap dengan item dan denda berjalan.
	 */
	public function getForUser($userId, $finePerDay)
	{
		$loans = $this->db->where('user_id', (int) $userId)
			->order_by('created_at', 'DESC')
			->get('loans')
			->result_array();

		if (empty($loans))
		{
			return array();
		}

		$loanIds = array_column($loans, 'id');
		$items = $this->getItemsWithBook($loanIds);
		$itemsByLoan = array();

		foreach ($items as $item)
		{
			$itemsByLoan[$item['loan_id']][] = $item;
		}

		foreach ($loans as &$loan)
		{
			$loan['items'] = isset($itemsByLoan[$loan['id']]) ? $itemsByLoan[$loan['id']] : array();
			$loan['item_count'] = count($loan['items']);
			$loan['late_days'] = 0;
			$loan['overdue_count'] = 0;
			$loan['unpaid_fine'] = 0;

			foreach ($loan['items'] as &$item)
			{
				$late = 0;

				if ($loan['status'] === 'borrowed' && ! empty($item['due_date']))
				{
					$late = $this->_lateDays($item['due_date']);
					$item['fine_preview'] = $late * (int) $finePerDay;
				}
				else
				{
					$item['fine_preview'] = (int) $item['fine_amount'];
				}

				$item['late_days'] = $late;

				if ($late > 0)
				{
					$loan['late_days'] = max($loan['late_days'], $late);
					$loan['overdue_count']++;
				}

				if ( ! $item['fine_paid'] && $item['fine_preview'] > 0)
				{
					$loan['unpaid_fine'] += $item['fine_preview'];
				}
			}
			unset($item);
		}
		unset($loan);

		return $loans;
	}

	public function getById($id)
	{
		return $this->db->where('id', (int) $id)->get('loans')->row_array();
	}

	public function getForUserById($id, $userId)
	{
		return $this->db->where('id', (int) $id)
			->where('user_id', (int) $userId)
			->get('loans')
			->row_array();
	}

	public function getAdminList(array $filters = array(), $limit = NULL, $offset = 0)
	{
		$this->db->select('loans.*, users.name AS user_name, users.email AS user_email,
			(SELECT COUNT(*) FROM loan_items li WHERE li.loan_id = loans.id) AS item_count', FALSE);
		$this->db->from('loans');
		$this->db->join('users', 'users.id = loans.user_id');
		$this->_applyAdminFilters($filters);

		if ($limit !== NULL)
		{
			$this->db->limit((int) $limit, (int) $offset);
		}

		return $this->db->order_by('loans.created_at', 'DESC')->get()->result_array();
	}

	public function countAdmin(array $filters = array())
	{
		$this->db->from('loans');
		$this->db->join('users', 'users.id = loans.user_id');
		$this->_applyAdminFilters($filters);

		return (int) $this->db->count_all_results();
	}

	public function getAdminDetail($id)
	{
		$loan = $this->db->select('loans.*, users.name AS user_name, users.email AS user_email, users.id AS user_id')
			->from('loans')
			->join('users', 'users.id = loans.user_id')
			->where('loans.id', (int) $id)
			->get()
			->row_array();

		if ( ! $loan)
		{
			return NULL;
		}

		$loan['items'] = $this->getItemsWithBook(array($loan['id']));

		return $loan;
	}

	/**
	 * Membuat reservasi dari keranjang. Stok langsung ditahan.
	 * Mengembalikan ID loan, atau FALSE dengan pesan di $lastError.
	 */
	public function createFromCart($userId, array $items, array $settings)
	{
		$this->lastError = NULL;

		if (empty($items))
		{
			$this->lastError = 'Keranjang masih kosong.';
			return FALSE;
		}

		$minDays = (int) $settings['min_loan_days'];
		$maxDays = (int) $settings['max_loan_days'];
		$deadline = date('Y-m-d 23:59:59', strtotime('+'.(int) $settings['pickup_deadline_days'].' days'));

		$this->db->trans_begin();

		foreach ($items as $item)
		{
			$duration = (int) $item['duration_days'];

			if ($duration < $minDays OR $duration > $maxDays)
			{
				$this->lastError = 'Durasi pinjam harus antara '.$minDays.' dan '.$maxDays.' hari.';
				$this->db->trans_rollback();
				return FALSE;
			}

			if ( ! $item['book_active'] OR ! $item['variant_active'])
			{
				$this->lastError = 'Buku "'.$item['title'].'" sudah tidak tersedia. Hapus dari keranjang.';
				$this->db->trans_rollback();
				return FALSE;
			}

			if ( ! $this->VariantModel->decrementStock($item['variant_id']))
			{
				$this->lastError = 'Stok "'.$item['title'].' - '.$item['variant_name'].'" habis. Perbarui keranjang.';
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		$this->db->insert('loans', array(
			'user_id'         => (int) $userId,
			'status'          => 'pending',
			'pickup_deadline' => $deadline,
			'created_at'      => date('Y-m-d H:i:s'),
		));

		$loanId = (int) $this->db->insert_id();

		$this->db->where('id', $loanId)->update('loans', array(
			'code' => 'PB-'.str_pad((string) $loanId, 6, '0', STR_PAD_LEFT),
		));

		foreach ($items as $item)
		{
			$this->db->insert('loan_items', array(
				'loan_id'       => $loanId,
				'book_id'       => (int) $item['book_id'],
				'variant_id'    => (int) $item['variant_id'],
				'duration_days' => (int) $item['duration_days'],
				'created_at'    => date('Y-m-d H:i:s'),
			));
		}

		$this->db->where('user_id', (int) $userId)->delete('cart_items');

		if ($this->db->trans_status() === FALSE)
		{
			$this->lastError = 'Reservasi gagal disimpan. Silakan coba lagi.';
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();

		return $loanId;
	}

	public function approve($loanId, $deadlineDays)
	{
		$this->db->trans_begin();

		$deadline = date('Y-m-d 23:59:59', strtotime('+'.(int) $deadlineDays.' days'));

		$this->db->where('id', (int) $loanId)
			->where('status', 'pending')
			->update('loans', array(
				'status'          => 'approved',
				'pickup_deadline' => $deadline,
			));

		$ok = $this->db->affected_rows() === 1;
		$ok ? $this->db->trans_commit() : $this->db->trans_rollback();

		return $ok;
	}

	public function reject($loanId, $reason)
	{
		$this->db->trans_begin();

		$this->db->where('id', (int) $loanId)
			->where('status', 'pending')
			->update('loans', array(
				'status'           => 'rejected',
				'rejection_reason' => $reason,
			));

		$ok = $this->db->affected_rows() === 1;

		if ($ok)
		{
			$this->restoreStock($loanId);
			$this->db->trans_commit();
		}
		else
		{
			$this->db->trans_rollback();
		}

		return $ok;
	}

	public function cancel($loanId, $userId)
	{
		$this->db->trans_begin();

		$this->db->where('id', (int) $loanId)
			->where('user_id', (int) $userId)
			->where_in('status', array('pending', 'approved'))
			->update('loans', array('status' => 'cancelled'));

		$ok = $this->db->affected_rows() === 1;

		if ($ok)
		{
			$this->restoreStock($loanId);
			$this->db->trans_commit();
		}
		else
		{
			$this->db->trans_rollback();
		}

		return $ok;
	}

	public function pickup($loanId)
	{
		$this->db->trans_begin();

		$pickedUpAt = date('Y-m-d H:i:s');

		$this->db->where('id', (int) $loanId)
			->where('status', 'approved')
			->update('loans', array(
				'status'       => 'borrowed',
				'picked_up_at' => $pickedUpAt,
			));

		$ok = $this->db->affected_rows() === 1;

		if ($ok)
		{
			$items = $this->db->where('loan_id', (int) $loanId)->get('loan_items')->result_array();

			foreach ($items as $item)
			{
				// Durasi dihitung sebagai hari kalender, termasuk tanggal pengambilan.
				$dueDate = date('Y-m-d', strtotime($pickedUpAt.' +'.((int) $item['duration_days'] - 1).' days'));

				$this->db->where('id', $item['id'])->update('loan_items', array('due_date' => $dueDate));
			}

			$this->db->trans_commit();
		}
		else
		{
			$this->db->trans_rollback();
		}

		return $ok;
	}

	public function returnBooks($loanId, $finePerDay)
	{
		$this->db->trans_begin();

		$returnedAt = date('Y-m-d H:i:s');

		$this->db->where('id', (int) $loanId)
			->where('status', 'borrowed')
			->update('loans', array(
				'status'      => 'returned',
				'returned_at' => $returnedAt,
			));

		$ok = $this->db->affected_rows() === 1;

		if ($ok)
		{
			$items = $this->db->where('loan_id', (int) $loanId)->get('loan_items')->result_array();

			foreach ($items as $item)
			{
				$late = $this->_lateDays($item['due_date'], $returnedAt);
				$fine = $late * (int) $finePerDay;

				$this->db->where('id', $item['id'])->update('loan_items', array(
					'returned_at' => $returnedAt,
					'fine_amount' => $fine,
					'fine_paid'   => 0,
				));
			}

			$this->restoreStock($loanId);
			$this->db->trans_commit();
		}
		else
		{
			$this->db->trans_rollback();
		}

		return $ok;
	}

	public function markFinePaid($loanId)
	{
		$this->db->where('loan_id', (int) $loanId)->update('loan_items', array('fine_paid' => 1));

		return $this->db->affected_rows() > 0;
	}

	/**
	 * Batalkan reservasi yang melewati batas konfirmasi/pengambilan.
	 * Dipanggil dari MY_Controller karena tidak ada cron.
	 */
	public function expireStale()
	{
		$stale = $this->db->select('id')
			->where_in('status', array('pending', 'approved'))
			->where('pickup_deadline IS NOT NULL', NULL, FALSE)
			->where('pickup_deadline <', date('Y-m-d H:i:s'))
			->get('loans')
			->result_array();

		foreach ($stale as $row)
		{
			$this->db->trans_begin();

			$this->db->where('id', (int) $row['id'])
				->where_in('status', array('pending', 'approved'))
				->update('loans', array('status' => 'expired'));

			if ($this->db->affected_rows() === 1)
			{
				$this->restoreStock($row['id']);
				$this->db->trans_commit();
			}
			else
			{
				$this->db->trans_rollback();
			}
		}
	}

	public function countByStatus($status)
	{
		return (int) $this->db->where('status', $status)->count_all_results('loans');
	}

	public function countActive()
	{
		return $this->countByStatus('borrowed');
	}

	/**
	 * Jumlah item terlambat milik satu customer (untuk notifikasi header).
	 */
	public function countUserOverdue($userId)
	{
		return (int) $this->db->from('loans l')
			->join('loan_items li', 'li.loan_id = l.id')
			->where('l.user_id', (int) $userId)
			->where('l.status', 'borrowed')
			->where('li.due_date <', date('Y-m-d'))
			->count_all_results();
	}

	/**
	 * Total denda yang belum dibayar milik satu customer.
	 */
	public function sumUserUnpaidFine($userId)
	{
		$row = $this->db->select('COALESCE(SUM(li.fine_amount), 0) AS total', FALSE)
			->from('loans l')
			->join('loan_items li', 'li.loan_id = l.id')
			->where('l.user_id', (int) $userId)
			->where('l.status', 'returned')
			->where('li.fine_paid', 0)
			->where('li.fine_amount >', 0)
			->get()
			->row_array();

		return (int) $row['total'];
	}

	public function countOverdue()
	{
		return (int) $this->db->from('loans l')
			->join('loan_items li', 'li.loan_id = l.id')
			->where('l.status', 'borrowed')
			->where('li.due_date <', date('Y-m-d'))
			->count_all_results();
	}

	public function getRecentPending($limit = 5)
	{
		return $this->db->select('loans.*, users.name AS user_name, users.email AS user_email')
			->from('loans')
			->join('users', 'users.id = loans.user_id')
			->where('loans.status', 'pending')
			->order_by('loans.created_at', 'DESC')
			->limit((int) $limit)
			->get()
			->result_array();
	}

	private function getItemsWithBook(array $loanIds)
	{
		if (empty($loanIds))
		{
			return array();
		}

		return $this->db->select('loan_items.*, books.title, books.slug AS book_slug, books.cover_url,
			book_variants.name AS variant_name, categories.name AS category_name')
			->from('loan_items')
			->join('books', 'books.id = loan_items.book_id')
			->join('book_variants', 'book_variants.id = loan_items.variant_id')
			->join('categories', 'categories.id = books.category_id')
			->where_in('loan_items.loan_id', $loanIds)
			->order_by('loan_items.id', 'ASC')
			->get()
			->result_array();
	}

	/**
	 * Kembalikan stok semua item pada loan (dipanggil sekali saat transisi keluar dari status aktif).
	 */
	private function restoreStock($loanId)
	{
		$items = $this->db->where('loan_id', (int) $loanId)->get('loan_items')->result_array();

		foreach ($items as $item)
		{
			$this->VariantModel->incrementStock($item['variant_id']);
		}
	}

	private function _lateDays($dueDate, $until = NULL)
	{
		if (empty($dueDate))
		{
			return 0;
		}

		$due = new DateTime(date('Y-m-d', strtotime($dueDate)));
		$end = new DateTime($until ? date('Y-m-d', strtotime($until)) : date('Y-m-d'));

		if ($end <= $due)
		{
			return 0;
		}

		return (int) $due->diff($end)->days;
	}

	private function _applyAdminFilters(array $filters)
	{
		if ( ! empty($filters['status']))
		{
			$this->db->where('loans.status', $filters['status']);
		}

		if ( ! empty($filters['q']))
		{
			$q = $filters['q'];
			$this->db->group_start()
				->like('loans.code', $q)
				->or_like('users.name', $q)
				->or_like('users.email', $q)
				->group_end();
		}
	}
}
