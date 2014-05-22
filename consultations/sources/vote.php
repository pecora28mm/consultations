<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Vote extends Record {
	public $id = 0;
	public $members_id = 0;
	public $consultation_hash = "";
	public $hash = "";
	public $day = 0;
	public $time = 0;
	
	protected $consultation;
	protected $answers;
	protected $user;

	function __construct($id = 0, db $db = null) {
		parent::__construct($db);
		$this->id = $id;
	}

	function charge($consultation, $member, $answers = array(), $day = null) {
		$this->consultation = $consultation;
		$this->member = $member;
		$this->answers = $answers;
		
		switch (true) {
			case !($this->consultation instanceof Consultation):
			case !($this->member instanceof Member):
				return false;
			default:
				$this->members_id = $this->member->id;
				$this->consultation_hash = $this->consultation->hash();
				$this->day = (isset($day) and (int)$day > 0) ? (int)$day : time();
				return true; 
		}
	}
	
	function hash() {
		return md5(serialize(array((string)$this->members_id, (string)$this->consultation_hash, $this->answers, (string)$this->day))); 
	}

	function is_coherent() {
		if ($this->hash() == $this->hash) {
			return true;
		} else {
			return false;
		}
	}

	function is_done() {
		$answers = new Answers();
		$answers->members_id = $this->members_id;
		$answers->consultation_hash = $this->consultation_hash;
		$answers->select();
		
		$answers_temp = $this->answers;
		$answers_done = array();
		foreach ($answers as $answer) {
			$answers_done[$answer->choice] = $answer->position;
		}
		$this->answers = $answers_done;
		if ($this->hash() === $this->hash) {
			$this->answers = $answers_temp;
			return true;
		} else {
			$this->answers = $answers_temp;
			return false;
		}
	}
	
	function show_summary() {
		$html = "<div class=\"consultation-message-light\">";
		$html .= __("Your vote has already been registered on <strong>%s</strong>.", array(date("d/m/Y H:i", $this->time)));
		$html .= "</div>";
		
		return $html;
	}
	
	function match_existing($patterns = array("name"), $table = "votes", $db = null) {
		return parent::match_existing($patterns, $table, $db);
	}

	function load($id = null, $table = "votes", $columns = null) {
		return parent::load($id, $table, $columns);
	}

	function db($db) {
		if ($db instanceof db) {
			$this->db = $db;
		}
	}
	
	function save_with_answers() {
		$this->save();
		if (is_array($this->answers)) {
			$answers_old = new Answers();
			$answers_old->members_id = $this->members_id;
			$answers_old->consultation_hash = $this->consultation_hash;
			$answers_old->select();
			$answers_old->delete();
			
			foreach ($this->answers as $choice => $position) {
				$answer = new Answer();
				$answer->members_id = $this->members_id;
				$answer->consultation_hash = $this->consultation_hash;
				$answer->choice = $choice;
				$answer->position = $position;
				$answer->save();
			}
		}
	}

	function save() {
		if (is_numeric($this->id) and $this->id != 0) {
			$this->id = $this->update();
		} else {
			$this->id = $this->insert();
		}
		return $this->id;
	}
	
	function insert() {
		$this->hash = $this->hash();
		$result = $this->db->id("
			INSERT INTO votes
			SET members_id = ".(int)$this->members_id.",
			consultation_hash = ".$this->db->quote($this->consultation_hash).",
			day = ".(int)$this->day.",
			hash = ".$this->db->quote($this->hash).",
			time = ".time()
		);
		$this->id = $result[2];
		$this->db->status($result[1], "i", __("vote"));

		return $this->id;
	}
	
	function update() {
		$this->hash = $this->hash();
		$result = $this->db->query("
			UPDATE votes
			SET members_id = ".(int)$this->members_id.",
			consultation_hash = ".$this->db->quote($this->consultation_hash).",
			day = ".(int)$this->day.",
			hash = ".$this->db->quote($this->hash).",
			time = ".time()."
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "u", __("vote"));

		return $this->id;
	}

	function delete() {
		$result = $this->db->query("
			DELETE FROM votes
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "d", __("vote"));

		return $this->id;
	}
	
	function is_deletable() {
		return true;
	}
}
