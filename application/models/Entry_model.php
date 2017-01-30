<?php

/**
 * Guestbook Entry Model for saving guestbook entries; there should be no guestbook updates as this is an immutable guest log.
 */
class Entry_model extends CI_Model {
	/**
	 * @var string
	 */
	public $guestbook_name;

	/**
	 * @var string
	 */
	public $guestbook_comment;

	/**
	 * @var string
	 */
	public $guestbook_email;

	/**
	 * @var string
	 */
	public $guestbook_timezone;

	public function setName($name)
	{
		$this->guestbook_name = $name;
	}

	public function getName()
	{
		return $this->guestbook_name;
	}

	public function setEmail($email)
	{
		$this->guestbook_email = $email;
	}

	public function getEmail()
	{
		return $this->guestbook_email;
	}

	public function setComment($comment)
	{
		$this->guestbook_comment = $comment;
	}

	public function getComment($comment)
	{
		return $this->guestbook_comment;
	}

	public function setTimezone($timezone)
	{
		$this->guestbook_timezone = $timezone;
	}

	public function getTimezone()
	{
		return $this->guestbook_timezone;
	}


	public function getCreated()
	{
		$this->guestbook_created;
	}

	public function __construct() 
	{
		parent::__construct();
	}

	public function get_last_ten_entries()
	{
		$query = $this->db->get("guestbook", 10);

		return $query->result();
	}

	public function insert_entry()
	{
		if ($this->is_unique()) {
			$this->db->insert('guestbook', (array)$this);
		} else {
			throw new \Exception("Duplicate e-mail; guest already exists in the database.");
		}

		return $this->db->insert_id();
	}

	/**
	 * Determine that the e-mail is unique
	 *
	 * @return boolean
	 */
	public function is_unique()
	{
		$this->db->select("guestbook_email")->from("guestbook")->where("guestbook_email", $this->guestbook_email);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return false;
		}

		return true;
	}
}