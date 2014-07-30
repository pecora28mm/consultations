<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Members_Comities extends Collector {
	function __construct(Db $db = null) {
		parent::__construct("Member_Comity", "members_comities", $db);
	}
	
	function comity_ids() {
		$comity_ids = array();
		foreach ($this as $member_comity) {
			$comity_ids[] = $member_comity->comity_id;
		}
		return $comity_ids;
	}

	function get_where() {
		$where = parent::get_where();
		
		if (isset($this->member_id)) {
			$where[] = "members_comities.member_id = ".(int)$this->member_id;
		}
		
		return $where;
	}
}
