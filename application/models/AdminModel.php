<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminModel extends CI_Model {

	public function findByEmail($email)
	{
		return $this->db->where('email', $email)->get('admins')->row_array();
	}

	public function findById($id)
	{
		return $this->db->where('id', (int) $id)->get('admins')->row_array();
	}
}
