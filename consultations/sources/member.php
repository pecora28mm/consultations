<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Member extends Record {
	public $id = 0;
	public $name = "";
	public $address = "";
	public $postcode = "";
	public $city = "";
	
	function __construct($id = 0, db $db = null) {
		parent::__construct($db);
		$this->id = $id;
	}

	function match_existing($patterns = array("name"), $table = "bureaux", $db = null) {
		return parent::match_existing($patterns, $table, $db);
	}

	function load($id = null, $table = "bureaux", $columns = null) {
		return parent::load($id, $table, $columns);
	}

	function db($db) {
		if ($db instanceof db) {
			$this->db = $db;
		}
	}
}
