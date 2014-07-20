<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

require_once __DIR__."/../sources/bootloader.php";

class tests_Answers extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"answers"
		);
	}
	
	function test_show_results_with_broda_method() {
		$answer = new Answer();
		$answer->members_id = 1;
		$answer->choice = "A";
		$answer->consultations_hash = "hash";
		$answer->position = 1;
		$answer->save();
		
		$answer = new Answer();
		$answer->members_id = 1;
		$answer->choice = "B";
		$answer->consultations_hash = "hash";
		$answer->position = 2;
		$answer->save();
		
		$answer = new Answer();
		$answer->members_id = 1;
		$answer->choice = "C";
		$answer->consultations_hash = "hash";
		$answer->position = 3;
		$answer->save();
		
		$answer = new Answer();
		$answer->members_id = 2;
		$answer->choice = "A";
		$answer->consultations_hash = "hash";
		$answer->position = 1;
		$answer->save();
		
		$answer = new Answer();
		$answer->members_id = 2;
		$answer->choice = "B";
		$answer->consultations_hash = "hash";
		$answer->position = 3;
		$answer->save();
		
		$answer = new Answer();
		$answer->members_id = 2;
		$answer->choice = "C";
		$answer->consultations_hash = "hash";
		$answer->position = 2;
		$answer->save();
		
		
		$answers = new Answers();
		$answers->select();
		
		$this->assertPattern("/Position #1 : A avec 2 points/", $answers->show_results_with_broda_method());
		$this->assertPattern("/Position #2 : B avec 5 points/", $answers->show_results_with_broda_method());
		$this->assertPattern("/Position #2 : C avec 5 points/", $answers->show_results_with_broda_method());
		
		$this->truncateTables("answers");
	}
}
