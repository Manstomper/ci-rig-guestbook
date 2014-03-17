<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_Model extends CI_Model
{
	/**
	 * @var string Table name
	 */
	private $table = 'gb_user';

	function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

	/**
	 * @param array $data email, password
	 * @return CI Database Result
	 */
	function login($data)
	{
		$data['password'] = hash('sha256', $data['password']);

		if ($result = $this->db->query('SELECT id, email, name, website FROM ' . $this->table . ' WHERE email = ? AND password = ?', $data)->row())
		{
			$this->session->set_userdata(array(
				'authUser' => $result,
			));

			return true;
		}

		return false;
	}

	function logout()
	{
		$this->session->unset_userdata('authUser');
	}
}