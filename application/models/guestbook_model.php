<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Guestbook_Model extends CI_Model
{
	/**
	 * @var string Table name
	 */
	private $table = 'gb_message';

	function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

	/**
	 * @param array $filter status, offset, limit
	 * @return CI Database Result
	 */
	function select(array $filter = array())
	{
		return $this->db->query('SELECT * FROM ' . $this->table . ' WHERE status <= ? ORDER BY status DESC, date DESC LIMIT ?, ?', $filter);
	}

	/**
	 * @param array $filter status, offset, limit
	 * @return CI Database Result
	 */
	function count(array $filter = array())
	{
		return $this->db->query('SELECT COUNT(*) AS count FROM ' . $this->table . ' WHERE status <= ?', $filter)->row()->count;
	}

	/**
	 * @param integer|string $id
	 * @return CI Database Result
	 */
	function getById($id)
	{
		return $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id = ?', array(
					(int) $id,
		));
	}

	/**
	 * @param array $data name, email, website, message
	 * @return integer Last insert ID
	 */
	function insert($data)
	{
		$data = $this->filterMessage($data);
		$data['status'] = $this->session->userdata('authUser') ? 1 : 2;

		$this->db->query('INSERT INTO ' . $this->table . ' (name, email, website, message, status, date) VALUES (?, ?, ?, ?, ?, NOW())', $data);

		return $this->db->insert_id();
	}

	/**
	 * @param array $data name, email, website, message, id
	 * @return integer Affected rows
	 */
	function update($data)
	{
		$data = $this->filterMessage($data);

		$this->db->query('UPDATE ' . $this->table . ' SET name = ?, email = ?, website = ?, message = ? WHERE id = ?', $data);

		return $this->db->affect_rows;
	}

	/**
	 * @param integer|string $id
	 * @return integer Affected rows
	 */
	function delete($id)
	{
		$this->db->query('DELETE FROM ' . $this->table . ' WHERE id = ?', array(
			(int) $id,
		));

		return $this->db->affect_rows;
	}

	/**
	 * @return boolean
	 */
	function unapprovedCount()
	{
		return (int) $this->db->query('SELECT COUNT(*) as count FROM ' . $this->table . ' WHERE status = 2')->row()->count;
	}

	/**
	 * @param integer|string $id
	 * @return integer Affected rows
	 */
	function approve($id)
	{
		$this->db->query('UPDATE ' . $this->table . ' SET status = 1 WHERE id = ?', array(
			(int) $id,
		));

		return $this->db->affect_rows;
	}

	/**
	 * @return integer Affected rows
	 */
	function approveAll()
	{
		$this->db->query('UPDATE ' . $this->table . ' SET status = 1');

		return $this->db->affect_rows;
	}

	/**
	 * @param array $data
	 * @return array Filtered data
	 */
	protected function filterMessage($data)
	{
		$arr['name'] = !empty($data['name']) ? $data['name'] : null;
		$arr['email'] = !empty($data['email']) ? $data['email'] : null;
		$arr['website'] = $data['website'];

		if (empty($arr['website']))
		{
			$arr['website'] = null;
		}
		elseif (!preg_match('/^https?:\/\//', $arr['website']))
		{
			$arr['website'] = 'http://' . $arr['website'];
		}

		$arr['message'] = '<p>' . preg_replace(array(
					'/\R{2,}/',
					'/\R{1}/',
					'/\s{2,}/',
						), array(
					'</p><p>',
					'<br />',
					' ',
						), $data['message']) . '</p>';

		if (!empty($data['id']))
		{
			$arr['id'] = (int) $data['id'];
		}

		return $arr;
	}
}