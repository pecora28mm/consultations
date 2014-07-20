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
	
	function test_clean() {
		$post = array (
			'consultation' => array(
				'id' => "42-test",
				'name' => "42ème test",
				'description' => "Desc. du 42ème test",
				'elements' => array (
				    'name' => '',
				    'period' => array (
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
				    ),
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
			'elements' => array (
			    'period' => array (
					'start' => mktime(0, 0, 0, 0, 0, 0),
					'stop' => mktime(0, 0, 0, 0, 0, 0),
			    ),
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
