<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Answer extends Record {
	public $id = 0;
	public $members_id = 0;
	public $consultations_hash = "";
	public $choice = "";
	public $position = 0;
	public $time = 0;

	function __construct($id = 0, db $db = null) {
		parent::__construct($db);
		$this->id = $id;
	}
	
	function match_existing($patterns = array("name"), $table = "votes", $db = null) {
		return parent::match_existing($patterns, $table, $db);
	}

	function load($id = null, $table = "votes", $columns = null) {
		return parent::load($id, $table, $columns);
	}

	function db($db) {
		if ($db instanceof db) {
			$this->db = $db;
		}
	}
	
	function save() {
		if (is_numeric($this->id) and $this->id != 0) {
			$this->id = $this->update();
		} else {
			$this->id = $this->insert();
		}
		return $this->id;
	}
	
	function insert() {
		$result = $this->db->id("
			INSERT INTO answers
			SET members_id = ".(int)$this->members_id.",
			consultations_hash = ".$this->db->quote($this->consultations_hash).",
			choice = ".$this->db->quote($this->choice).",
			position = ".(int)$this->position.",
			time = ".time()
		);
		$this->id = $result[2];
		$this->db->status($result[1], "i", __("answer"));

		return $this->id;
	}
	
	function update() {
		$result = $this->db->query("
			UPDATE answers
			SET members_id = ".(int)$this->members_id.",
			consultations_hash = ".$this->db->quote($this->consultations_hash).",
			choice = ".$this->db->quote($this->choice).",
			position = ".(int)$this->position.",
			time = ".time()."
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "u", __("answer"));

		return $this->id;
	}

	function delete() {
		$result = $this->db->query("
			DELETE FROM answers
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "d", __("answer"));

		return $this->id;
	}
	
	function is_deletable() {
		return true;
	}
}
