<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Consultation {
	public $file;
	public $elements;

	function charge($filename) {
		if (file_exists($filename)) {
			require $filename;
		}
		$this->elements = $elements;
	}
	
	function charge_next() {
		require __DIR__."/../configuration/elements/201406-groupe-parlementaire.php";
		$this->elements = $elements;
	}
	
	function hash() {
		return md5(serialize($this->elements));
	}
	
	function is_open() {
		if (!isset($this->elements['period']['start']) or !isset($this->elements['period']['stop'])) {
			return false;
		}

		if ($this->elements['period']['start'] <= time() and time() <= $this->elements['period']['stop']) {
			return true;
		} else {
			return false;
		}
	}
	
	function show_opened_sign() {
		$html = "<div class=\"consultation-message\">";
		$html .= __("A consultation is currently open: <strong>%s</strong>.", array($this->elements['question']));
		$html .= "</div>";
		
		return $html;
	}
	
	function url_to_preambule() {
		return $GLOBALS['config']['url']."index.php?page=preambule.php";
	}
	
	function url_to_opinions() {
		return $GLOBALS['config']['url']."index.php?page=opinions.php";
	}
	
	function url_to_vote() {
		return $GLOBALS['config']['url']."index.php?page=vote.php";
	}
	
	function url_to_summary() {
		return $GLOBALS['config']['url']."index.php?page=summary.php";
	}
	
	function url_to_thankyou() {
		return $GLOBALS['config']['url']."index.php?page=thankyou.php";
	}
	
	function show_procedure() {
		$html = "<h1>".__("Procedure details")."</h1>";
		$html .= "<ol>";
		$html .= "<li>".Html_Tag::a($this->url_to_preambule(), __("Preambule : list of facts"))."</li>";
		$html .= "<li>".Html_Tag::a($this->url_to_opinions(), __("Opinions : list of views"))."</li>";
		$html .= "<li>".Html_Tag::a($this->url_to_vote(), __("Vote"))."</li>";
		$html .= "<li>".Html_Tag::a($this->url_to_summary(), __("Summary"))."</li>";
		$html .= "</ol>";
		
		return $html;
	}

	function show_preambule() {
		if (!isset($this->elements['preambules'])) {
			return $this->show_closed_sign();
		}
		
		$html = "<h1>".__("Preambule : list of facts")."</h1>";
		$html .= "<ul>";
		foreach ($this->elements['preambules'] as $url => $preambule) {
			$html .= "<li>".Html_Tag::a($url, $preambule, array('target' => "_blank"))."</li>";
		}
		$html .= "</ul>";
		$html .= "<p class=\"consultation-next btn btn-success\">".Html_Tag::a($this->url_to_opinions(), __("Move on to opinions"))."</p>";
		
		return $html;
	}
	
	function show_opinions() {
		if (!isset($this->elements['opinions'])) {
			return $this->show_closed_sign();
		}
		
		$html = "<h1>".__("Opinions : list of views")."</h1>";
		$html .= "<ul>";
		foreach ($this->elements['opinions'] as $url => $opinion) {
			$html .= "<li>".Html_Tag::a($url, $opinion, array('target' => "_blank"))."</li>";
		}
		$html .= "</ul>";
		$html .= "<p class=\"consultation-next btn btn-success\">".Html_Tag::a($this->url_to_vote(), __("Move on to vote"))."</p>";
		
		return $html;
	}
	
	function show_vote() {
		if (!isset($this->elements['question']) or !isset($this->elements['choices'])) {
			return $this->show_closed_sign();
		}
		
		$html = "<h1>".__("Let's vote")."</h1>";
		
		$html .= "<form method=\"post\" action=\"\">";
		
		$html .= "<div class=\"consultation-question\">".$this->elements['question']."</div>";
		
		$i_max = count($this->elements['choices']);
		$html .= "<div class=\"consultation-choices\">";
		$html .= "<table>";
		$html .= "<tr>";
		$html .= "<th></th>";
		for ($i = 1; $i <= $i_max; $i++) {
			$html .= "<th>".__("Choice n°%s", array($i))."</th>";
		}
		$html .= "</tr>";
		foreach ($this->elements['choices'] as $key => $value) {
			$html .= "<tr>";
			$html .= "<td>".$key."</td>";
			for ($i = 1; $i <= $i_max; $i++) {
				$choice = new Html_Input("consultation[answers][".$key."]", $i, "radio");
				$html .= "<td>".$choice->input()."</td>";
			}
			$html .= "</tr>";
		}
		$html .= "</table>";
		$html .= "</div>";
		
		$vote = new Html_Input("consultation[vote]", __("Vote"), "submit");
		$vote->properties['class'] = "btn btn-success consultation-submit";
		$html .= $vote->input();
		
		$html .= "</form>";
				
		return $html;
	}
	
	function show_summary($member) {
		$vote = new Vote();
		$vote->charge($this, $member);
		if ($vote->match_existing(array("members_id", "consultation_hash"))) {
			$vote->load();
		}
		if ($vote->is_done()) {
			$vote->load();
			$html = "<div class=\"consultation-information\">";
			$html .= __("Your answers were found : you'll find them below.");
			$html .= "</div>";
			
			$answers = new Answers();
			$answers->consultation_hash = $this->hash();
			$answers->members_id = $vote->members_id;
			$answers->set_order("position", "ASC");
			$answers->select();
			
			$html .= "<dl class=\"consultation-answers dl-horizontal\">";
			foreach ($answers as $answer) {
				$html .= "<dt>".$answer->choice."</dt>";
				$html .= "<dd>".$answer->position."</dd>";
			}
			$html .= "</dl>";
			
			$html .= "<div class=\"consultation-information\">";
			$html .= __("If you want to change your answers, you can still change them.");
			$html .= "</div>";
			$html .= "<p class=\"consultation-next btn btn-success\">".Html_Tag::a($this->url_to_thankyou(), __("Agree with this vote"))."</p>";
			$html .= "<p class=\"consultation-next btn btn-warning\">".Html_Tag::a($this->url_to_vote(), __("Move back to vote"))."</p>";
				
		} else {
			$html = "<div class=\"consultation-information\">";
			$html .= __("Your answers were not found. Please vote first.");
			$html .= "</div>";
			$html .= "<p class=\"consultation-next btn btn-warning\">".Html_Tag::a($this->url_to_vote(), __("Move back to vote"))."</p>";
		}
		return $html;
	}
	
	function show_thankyou() {
		$html = "<div class=\"consultation-information\">";
		$html .= "<p>".__("Thank you for voting.")."</p>";
		$html .= "<p>".__("This consultation will be closed definitely on %s.", array(date("d/m/Y H:i", $this->elements['period']['stop'])))."</p>";
		$html .= "</div>";
		
		return $html;
	}

	function clean_answers($answers) {
		$answers_cleaned = $answers;
		return $answers_cleaned;
	}
	
	function are_answers_coherent($answers) {
		$values = array();
		for ($i = 1; $i <= count($this->elements['choices']); $i++) {
			$values[] = $i;
		}
		switch (true) {
			case array_diff(array_keys($answers), array_keys($this->elements['choices'])) != array():
			case array_diff($values, array_values($answers)) != array(): 
				return false;
			default:
				return true;
		}
	}

	function show_closed_sign() {
		$html = "<div class=\"consultation-error\">";
		$html .= __("No consultation is currently open.");
		$html .= "</div>";
		
		return $html;
	}
	
	function show_problem_with_save() {
		$html = "<div class=\"consultation-error\">";
		$html .= __("Your answers were not saved. Please contact the technical team : tech@nouvelledonne.fr.");
		$html .= "</div>";
		
		return $html;
	}
	
	function show_problem_with_answers() {
		$html = "<div class=\"consultation-error\">";
		$html .= __("Your answers are not coherent. Please make sure you're ranking all the possibles choices.");
		$html .= "</div>";
		
		return $html;
	}
}
