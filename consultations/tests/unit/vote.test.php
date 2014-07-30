<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

require_once __DIR__."/../sources/bootloader.php";

class tests_Vote extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"answers",
			"votes"
		);
	}
	
	function test_is_done() {
		$vote = new Vote();
		$this->assertTrue($vote->charge(new Consultation(), new Member(1), array('PSE' => "1", 'PPE' => "2"), mktime(0, 0, 0, 22, 5, 2014)));
		$vote->save_with_answers();
		
		$answers = new Answers();
		$answers->select();
		$this->assertEqual(count($answers), 2);
		
		$this->assertTrue($vote->is_done());
		
		$this->truncateTables("answers", "votes");
	}

	function test_is_coherent() {
		$vote = new Vote();
		$this->assertTrue($vote->charge(new Consultation(), new Member(1), array(), mktime(0, 0, 0, 22, 5, 2014)));
		$vote->save();
		
		$vote->load();
		$this->assertTrue($vote->is_coherent());
		
		$this->db->query("UPDATE votes SET members_id = 2 WHERE id = 1");
		$vote->load();
		$this->assertFalse($vote->is_coherent());
		
		$this->truncateTables("votes");
	}
	
	function test_charge() {
		$vote = new Vote();
		$this->assertTrue($vote->charge(new Consultation(), new Member(1)));
		$this->assertEqual($vote->members_id, 1);
		$this->assertEqual($vote->consultations_hash, "40cd750bba9870f18aada2478b24840a");
	}
	
	function test_save_load() {
		$vote = new Vote();
		$this->assertTrue($vote->charge(new Consultation(), new Member(1)));
		$this->assertTrue($vote->save());

		$vote_loaded = new Vote();
		$vote_loaded->id = 1;
		$vote_loaded->load();
		
		$this->assertEqual($vote_loaded->members_id, $vote->members_id);
		$this->assertEqual($vote_loaded->consultations_hash, $vote->consultations_hash);
		$this->assertEqual($vote_loaded->day, $vote->day);
		
		$this->truncateTable("votes");
	}
	
	function test_update() {
		$vote = new Vote();
		$vote->charge(new Consultation(), new Member(1));
		$vote->save();
		
		$vote_loaded = new Vote();
		$vote_loaded->id = 1;
		$vote_loaded->members_id = 2;
		$this->assertTrue($vote_loaded->update());
		
		$vote_loaded2 = new Vote();
		$vote_loaded2->id = 1;
		$vote_loaded2->load();
		$this->assertNotEqual($vote_loaded2->members_id, $vote->members_id);

		$this->truncateTable("votes");
	}
	
	function test_delete() {
		$vote = new Vote();
		$vote->charge(new Consultation(), new Member(1));
		$vote->save();
		
		$vote_loaded = new Vote();
		$this->assertTrue($vote_loaded->load(1));
		
		$this->assertTrue($vote->delete());
		
		$this->assertFalse($vote_loaded->load(1));
		
		$this->truncateTable("votes");
	}
}
