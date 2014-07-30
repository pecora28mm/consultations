<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Member_Comity extends Record {
	public $id = 0;
	public $member_id = 0;
	public $comity_id = 0;
	
	function __construct($id = 0, db $db = null) {
		parent::__construct($db);
		$this->id = $id;
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
			INSERT INTO members_comities
			SET member_id = ".(int)$this->member_id.",
			comity_id = ".(int)$this->comity_id
		);
		$this->id = $result[2];
		$this->db->status($result[1], "i", __("member / comity"));
	
		return $this->id;
	}
	
	function update() {
		$result = $this->db->query("
			UPDATE members_comities
			SET member_id = ".(int)$this->member_id.",
			comity_id = ".(int)$this->comity_id."
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "u", __("member / comity"));
	
		return $this->id;
	}
	
	function delete() {
		$result = $this->db->query("
			DELETE FROM members_comities
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "d", __("member / comity"));
	
		return $this->id;
	}
	
	function is_deletable() {
		return true;
	}
	
}
