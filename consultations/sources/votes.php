<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Votes extends Collector {
	function __construct(Db $db = null) {
		parent::__construct("Vote", "votes", $db);
	}
	
	function consultations_hashes() {
		$consultations_hashes = array();
		foreach ($this as $vote) {
			$consultations_hashes[] = $vote->consultations_hash;
		}
		return array_unique($consultations_hashes);
	}

	function get_where() {
		$where = parent::get_where();
		
		if (isset($this->members_id)) {
			$where[] = "votes.members_id = ".(int)$this->members_id;
		}
		if (isset($this->consultations_id)) {
			$where[] = "votes.consultations_id = ".$this->db->quote($this->consultations_id);
		}
		if (isset($this->consultations_hash)) {
			$where[] = "votes.consultations_hash = ".$this->db->quote($this->consultations_hash);
		}
		
		return $where;
	}
}
