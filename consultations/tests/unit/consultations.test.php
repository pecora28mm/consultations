<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

require_once __DIR__."/../sources/bootloader.php";

class tests_Consultation extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"answers",
			"consultations",
			"votes"
		);
	}
	
	function test_manage() {
		$consultations = new Consultations();
		$consultations->select();
		$this->assertEqual($consultations->manage(), "");
		
		$consultation = new Consultation();
		$consultation->name = "Nouvelle consultation";
		$consultation->description = "Nouvelle consultation en plus long";
		$consultation->save();
		
		$consultations = new Consultations();
		$consultations->select();
		$this->assertPattern("/Nouvelle consultation/", $consultations->manage());
		$this->assertPattern("/admin\.php\?page=consultation\.php&consultations_id=".$consultation->id."/", $consultations->manage());
		
		$this->truncateTables("consultations");
	}
}
