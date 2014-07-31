<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

require_once __DIR__."/../sources/bootloader.php";

class tests_Preparation extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"consultations",
			"members",
			"members_comities"
		);
	}
	
	function test_charge_member_and_consultation_with_key() {
		$consultation = new Consultation();
		$consultation->name = "Première consultation";
		$consultation->description = "Première description";
		$consultation->start = strtotime("-2 days", time());
		$consultation->stop = strtotime("+2 days", time());
		$consultation->comity_id = 1;
		$consultation->save();
		
		$member = new Member();
		$member->nom = "Penet";
		$member->email = "perrick@noparking.net";
		$member->save();
		
		$preparation = new Preparation();
		$key = $preparation->encode_member_consultation($member, $consultation);
		
		$this->assertTrue($preparation->charge_member_and_consultation_with_key($key));
		
		$this->truncateTables("consultations", "members");
	}

	function test_encode_member_consultation() {
		$GLOBALS['config']['url'] = "http://localhost/~perrick/nouvelledonne-consultations/consultations/www/";
		
		$preparation = new Preparation();
		$this->assertEqual("MXwxfGE5MDMzMDhjODZlZDczY2I4MDUyY2E3YTA2MTNhNDE0", $preparation->encode_member_consultation(new Member(1), new Consultation(1)));
	}
	
	function test_charge_open_consultations_for_member() {
		$preparation = new Preparation();
		$this->assertFalse($preparation->charge_open_consultations_for_member());
		
		$consultation = new Consultation();
		$consultation->name = "Première consultation";
		$consultation->description = "Première description";
		$consultation->start = strtotime("-2 days", time());
		$consultation->stop = strtotime("+2 days", time());
		$consultation->comity_id = 1;
		$consultation->save();
		
		$this->assertFalse($preparation->charge_open_consultations_for_member());

		$member = new Member();
		$member->nom = "Penet";
		$member->email = "perrick@noparking.net";
		$member->save();

		$this->assertFalse($preparation->charge_open_consultations_for_member($member));

		$member_comity = new Member_Comity();
		$member_comity->member_id = $member->id;
		$member_comity->comity_id = 1;
		$member_comity->save();
		
		$this->assertTrue($preparation->charge_open_consultations_for_member($member));
		$this->assertEqual(count($preparation->consultations), 1);
		
		$consultation = new Consultation();
		$consultation->name = "Deuxième consultation";
		$consultation->description = "Deuxième description";
		$consultation->start = strtotime("+2 days", time());
		$consultation->stop = strtotime("+4 days", time());
		$consultation->comity_id = 1;
		$consultation->save();
		
		$this->assertTrue($preparation->charge_open_consultations_for_member($member));
		$this->assertEqual(count($preparation->consultations), 1);
		
		$this->truncateTables("consultations");
	}
	
	function test_ask_for_email() {
		$preparation = new Preparation();
		$this->assertPattern("/preparation\[email\]/", $preparation->ask_for_email());
	}
}
