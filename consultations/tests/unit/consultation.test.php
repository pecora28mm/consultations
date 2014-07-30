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
	
	function test_save() {
		$consultation = new Consultation();
		$consultation->name = "Première consultation";
		$consultation->description = "Première description";
		$consultation->comity_id = 12;
		$consultation->start = 123;
		$consultation->stop = 1234;
		$this->assertTrue($consultation->save());
		
		$consultation->comity_id = 13;
		$this->assertTrue($consultation->save());
		
		$consultation_loaded = new Consultation();
		$consultation_loaded->load($consultation->id);
		$this->assertEqual($consultation_loaded->id, $consultation->id);
		$this->assertEqual($consultation_loaded->name, $consultation->name);
		$this->assertEqual($consultation_loaded->description, $consultation->description);
		$this->assertEqual($consultation_loaded->comity_id, $consultation->comity_id);
		$this->assertEqual($consultation_loaded->start, $consultation->start);
		$this->assertEqual($consultation_loaded->stop, $consultation->stop);
		
		$this->truncateTables("consultations");
	}

	function test_clean() {
		$post = array (
			'consultation' => array(
				'id' => "42-test",
				'name' => "42ème test",
				'description' => "Desc. du 42ème test",
				'comity_id' => "42",
				'start' =>  array (
				  'd' => '',
				  'm' => '',
				  'Y' => '',
				),
				'stop' => array (
				  'd' => '',
				  'm' => '',
				  'Y' => '',
				),
				'elements' => array (
				    'preambules' => array (
				      1 => array (
				        'url' => '',
				        'title' => '',
				      ),
				      2 => array (
				        'url' => '',
				        'title' => '',
				      ),
				      3 => array (
				        'url' => '',
				        'title' => '',
				      ),
				    ),
				    'opinions' => array (
				      0 => array (
				        'url' => '',
				        'title' => '',
				      ),
				      1 => array (
				        'url' => '',
				        'title' => '',
				      ),
				      2 => array (
				        'url' => '',
				        'title' => '',
				      ),
				    ),
				    'question' => '',
				    'choices' => array (
				      0 => array (
				        'tag' => '',
				        'description' => '',
				      ),
				      1 => array (
				        'tag' => '',
				        'description' => '',
				      ),
				      2 => array (
				        'tag' => '',
				        'description' => '',
				      ),
				    ),
				  ),
			),
			'save' => 'Sauvegarder',
		);
		
		$cleaned = array (
			'id' => "42",
			'name' => "42ème test",
			'description' => "Desc. du 42ème test",
			'comity_id' => "42",
			'start' => mktime(0, 0, 0, 0, 0, 0),
			'stop' => mktime(0, 0, 0, 0, 0, 0),
			'elements' => array (
			    'preambules' => array(), 
			    'opinions' => array(),
			    'question' => '',
			    'choices' => array(),
			),
		);
		$consultation = new Consultation();
		$this->assertEqual($consultation->clean($post['consultation']), $cleaned);
	}
	
	function test_are_answers_coherent() {
		require __DIR__."/elements/201406-groupe-parlementaire.php";
		
		$consultation = new Consultation();
		$consultation->name = "groupe parlementaire";
		$consultation->elements = $elements;
		
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
