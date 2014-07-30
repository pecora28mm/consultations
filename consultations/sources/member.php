<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Member extends Record {
	public $id = 0;
	public $sexe = "";
	public $prenom = "";
	public $nom = "";
	public $nom_std = "";
	public $email = "";
	public $adresse1 = "";
	public $adresse2 = "";
	public $codepostal = "";
	public $pays_id = 0;
	public $commune_id = 0;
	public $commune_etranger = 0;
	public $tel = "";
	public $mob = "";
	public $created = 0;
	public $modified = 0;
	public $active = 0;
	public $mail_optin = 0;
	public $mail_sync_status = 0;
	
	function __construct($id = 0, db $db = null) {
		parent::__construct($db);
		$this->id = $id;
	}

	function match_existing($patterns = array("nom"), $table = "members", $db = null) {
		return parent::match_existing($patterns, $table, $db);
	}

	function load($id = null, $table = "members", $columns = null) {
		return parent::load($id, $table, $columns);
	}
	
	function comity_ids() {
		$members_comities = new Members_Comities($this->db);
		$members_comities->member_id = $this->id;
		$members_comities->select();
		return $members_comities->comity_ids();
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
			INSERT INTO members
			SET nom = ".$this->db->quote($this->nom).",
			email = ".$this->db->quote($this->email)
		);
		$this->id = $result[2];
		$this->db->status($result[1], "i", __("member"));
	
		return $this->id;
	}
	
	function update() {
		$result = $this->db->query("
			UPDATE members
			SET nom = ".$this->db->quote($this->nom).",
			email = ".$this->db->quote($this->email)."
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "u", __("member"));
	
		return $this->id;
	}
	
	function delete() {
		$result = $this->db->query("
			DELETE FROM members
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "d", __("member"));
	
		return $this->id;
	}
	
	function is_deletable() {
		return true;
	}
	
}
