<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_email_workflow extends CI_Migration 
{
	public function up()
	{
		$this->load->database();
		$this->dbforge->add_field([
			"email_id" => [
				"type" => "INT",
				"constraint" => 5,
				"unsigned" => true,
				"auto_increment" => true
			],
			"email_subject" => [
				"type" => "VARCHAR",
				"null" => true
			],
			"email_email" => [
				"type" => "VARCHAR",
				"constraint" => 255,
				"unique" => true
			],
			"email_message" => [
				"type" => "TEXT",
			],
			"email_created" => [
				"type" => "TIMESTAMP"
			],
			"email_send" => [
				"type" => "TIMESTAMP"
			],
			"guestbook_id" => [
				"type" => "INT",
				"unsigned" => true,
			]
		]);

		$this->dbforge->add_key("email_id", true);
		$this->db->query("ALTER TABLE email_workflow ADD CONSTRAINT email_guestbook_id_fk FOREIGN KEY (guestbook_id) REFERENCES guestbook ON DELETE CASCADE;");
		$this->dbforge->create_table("email_workflow");
	}

	function down() {
		$this->dbforge->drop_table("email_workflow");
	}
}