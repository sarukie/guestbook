<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_guestbook extends CI_Migration 
{
	public function up()
	{
		$this->dbforge->add_field([
			"guestbook_id" => [
				"type" => "INT",
				"constraint" => 5,
				"unsigned" => true,
				"auto_increment" => true
			],
			"guestbook_firstname" => [
				"type" => "VARCHAR",
				"constraint" => 100
			],
			"guestbook_lastname" => [
				"type" => "VARCHAR",
				"constraint" => 100
			],
			"guestbook_comment" => [
				"type" => "TEXT",
				"null" => true
			],
			"guestbook_email" => [
				"type" => "VARCHAR",
				"constraint" => 255
			],
			"guestbook_created" => [
				"type" => "datetime",
				"default" => "0000-00-00 00:00:00"
			]
		]);
	}


	function down() {
		$this->dbforge->drop_table("guestbook");
	}
}
