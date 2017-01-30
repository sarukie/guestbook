<?php

/**
 * Guestbook Email Workflow Model for emailing workflow
 */
class Emailworkflow_model extends CI_Model {
	/**
	 * @var string
	 */
	public $email_email;

	/**
	 * @var string
	 */
	public $email_subject;

	/**
	 * @var string
	 */
	public $email_message;

	/**
	 * @var string
	 */
	public $email_created;

	/**
	 * @var string
	 */
	public $email_send;

	/**
	 * Get the last ten entries
	 *
	 * @return array
	 */
	public function get_last_ten_entries()
	{
		$query = $this->db->get("email_workflow", 10);

		return $query->result();
	}

	/**
	 * Insert a new entry
	 */
	public function insert_entry()
	{
		$this->db->insert("email_workflow", $this);
	}
}