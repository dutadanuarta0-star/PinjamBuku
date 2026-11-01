<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SettingModel extends CI_Model {

	/**
	 * Semua pengaturan sebagai array key => value.
	 */
	public function getAll()
	{
		$rows = $this->db->get('settings')->result_array();
		$settings = array();

		foreach ($rows as $row)
		{
			$settings[$row['setting_key']] = $row['setting_value'];
		}

		return $settings;
	}

	public function get($key, $default = NULL)
	{
		$row = $this->db->where('setting_key', $key)->get('settings')->row_array();

		return $row ? $row['setting_value'] : $default;
	}

	/**
	 * Simpan banyak pengaturan sekaligus.
	 */
	public function setMany(array $data)
	{
		foreach ($data as $key => $value)
		{
			$this->db->replace('settings', array(
				'setting_key'   => $key,
				'setting_value' => $value,
			));
		}
	}
}
