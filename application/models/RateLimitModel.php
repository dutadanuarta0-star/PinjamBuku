<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RateLimitModel extends CI_Model {

	/**
	 * Catat satu percobaan. Mengembalikan FALSE jika sedang diblokir.
	 */
	public function attempt($action, $identifier, $maxAttempts = 5, $windowSeconds = 900)
	{
		$key = $action.':'.hash('sha256', (string) $identifier);
		$now = date('Y-m-d H:i:s');
		$row = $this->db->where('identifier', $key)->get('rate_limits')->row_array();

		if ($row && ! empty($row['blocked_until']) && $row['blocked_until'] > $now)
		{
			return FALSE;
		}

		$windowStart = date('Y-m-d H:i:s', time() - (int) $windowSeconds);

		if ( ! $row OR $row['window_started_at'] < $windowStart)
		{
			$this->db->replace('rate_limits', array(
				'identifier'        => $key,
				'attempts'          => 1,
				'window_started_at' => $now,
				'blocked_until'     => NULL,
			));

			return TRUE;
		}

		$attempts = (int) $row['attempts'] + 1;

		if ($attempts > (int) $maxAttempts)
		{
			$this->db->where('identifier', $key)->update('rate_limits', array(
				'attempts'      => $attempts,
				'blocked_until' => date('Y-m-d H:i:s', time() + (int) $windowSeconds),
			));

			return FALSE;
		}

		$this->db->where('identifier', $key)->update('rate_limits', array('attempts' => $attempts));

		return TRUE;
	}

	public function clear($action, $identifier)
	{
		$key = $action.':'.hash('sha256', (string) $identifier);
		$this->db->where('identifier', $key)->delete('rate_limits');
	}
}
