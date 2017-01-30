<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_guestbook extends CI_Migration 
{
	public function up()
	{
		$this->load->database();
		$this->dbforge->add_field([
			"guestbook_id" => [
				"type" => "INT",
				"constraint" => 5,
				"unsigned" => true,
				"auto_increment" => true
			],
			"guestbook_name" => [
				"type" => "VARCHAR",
				"constraint" => 100
			],
			"guestbook_comment" => [
				"type" => "TEXT",
				"null" => true
			],
			"guestbook_email" => [
				"type" => "VARCHAR",
				"constraint" => 255,
				"unique" => true
			],
			"guestbook_created" => [
				"type" => "TIMESTAMP"
			],
			"guestbook_timezone" => [
				"type" => "VARCHAR",
				"constraint" => 128
			]
		]);

		$this->dbforge->add_key("guestbook_id", true);
		$this->dbforge->create_table("guestbook");

		// Because there appeared to be an issue with pg_query allowing the creation of the created row with a default
		// value, I opted to throw an alter in here.
		$this->db->query("ALTER TABLE guestbook ALTER guestbook_created SET DEFAULT CURRENT_TIMESTAMP;");
	}


	function down() {
		$this->dbforge->drop_table("guestbook");
	}
}
