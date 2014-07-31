<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

require_once __DIR__."/../sources/bootloader.php";

class tests_Members extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"members",
			"members_comities"
		);
	}
	
	function tests_select() {
		$member = new Member();
		$member->email = "perrick@comite1.fr";
		$member->nom = "Comité 1";
		$member->save();
		
		$members = new Members();
		$members->select();
		$this->assertEqual(count($members), 1);
		
		$members = new Members();
		$members->comity_id = 1;
		$members->select();
		
		$this->assertEqual(count($members), 0);
		
		$member_comity = new Member_Comity();
		$member_comity->member_id = $member->id;
		$member_comity->comity_id = 1;
		$member_comity->save();
		
		$members = new Members();
		$members->comity_id = 1;
		$members->select();
		
		$this->assertEqual(count($members), 1);

		$this->truncateTables("members", "members_comities");
	}
}
