<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

require_once __DIR__."/../sources/bootloader.php";

class tests_Consultation extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"answers",
			"votes"
		);
	}
	
	function test_are_answers_coherent() {
		$consultation = new Consultation();
		$consultation->charge(__DIR__."/elements/201406-groupe-parlementaire.php");
		
		$answers = array("PSE" => '1', "PVE" => '1', "PGE" => '1', "000" => '1', );
		$this->assertFalse($consultation->are_answers_coherent($answers));

		$answers = array("PSE" => '4', "PVE" => '2', "PGE" => '1', "000" => '1', );
		$this->assertFalse($consultation->are_answers_coherent($answers));

		$answers = array("PSE" => '7', "PVE" => '3', "PGE" => '2', "000" => '1', );
		$this->assertFalse($consultation->are_answers_coherent($answers));

		$answers = array("PSE" => '4', "PVE" => '3', "PGE" => '2', "000" => '1', );
		$this->assertTrue($consultation->are_answers_coherent($answers));

		$answers = array("PVE" => '3', "PGE" => '2', "000" => '1', );
		$this->assertFalse($consultation->are_answers_coherent($answers));

		$answers = array("PVE" => '6', "PGE" => '2', "000" => '1', );
		$this->assertFalse($consultation->are_answers_coherent($answers));

		$answers = array("PSE" => '4', "PXX" => '3', "PGE" => '2', "000" => '1', );
		$this->assertFalse($consultation->are_answers_coherent($answers));
	}
}
