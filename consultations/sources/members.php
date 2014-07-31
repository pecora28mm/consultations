<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Members extends Collector {
	function __construct(Db $db = null) {
		parent::__construct("Member", "members", $db);
	}
	
	function get_where() {
		$where = parent::get_where();
		
		if (isset($this->id)) {
			$where[] = "members.id = ".(int)$this->id;
		}
		if (isset($this->comity_id)) {
			$where[] = "members.id IN (SELECT members_comities.member_id FROM members_comities WHERE members_comities.comity_id=".(int)$this->comity_id.")";
		}
		
		return $where;
	}
}
