<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserModel extends CI_Model {

	public function findByEmail($email)
	{
		return $this->db->where('email', $email)->get('users')->row_array();
	}

	public function findById($id)
	{
		return $this->db->where('id', (int) $id)->get('users')->row_array();
	}

	public function emailExists($email, $exceptId = NULL)
	{
		$this->db->where('email', $email);

		if ($exceptId !== NULL)
		{
			$this->db->where('id !=', (int) $exceptId);
		}

		return $this->db->count_all_results('users') > 0;
	}

	public function create(array $data)
	{
		$this->db->insert('users', array(
			'name'          => $data['name'],
			'email'         => $data['email'],
			'password_hash' => $data['password_hash'],
			'created_at'    => date('Y-m-d H:i:s'),
		));

		return (int) $this->db->insert_id();
	}

	public function touchLogin($id)
	{
		$this->db->where('id', (int) $id)->update('users', array(
			'last_login_at' => date('Y-m-d H:i:s'),
		));
	}
}
