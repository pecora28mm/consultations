<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Consultation extends Record {
	public $id = 0;
	public $name = "";
	public $description = "";
	public $comity_id = 0;
	public $start = 0;
	public $stop = 0;
	public $elements = array();
	public $hash = "";
	public $time = 0;

	function __construct($id = 0, db $db = null) {
		parent::__construct($db);
		$this->id = $id;
	}
	
	function match_existing($patterns = array("name"), $table = "consultations", $db = null) {
		return parent::match_existing($patterns, $table, $db);
	}

	function load($id = null, $table = "consultations", $columns = null) {
		$result = parent::load($id, $table, $columns);
		if (!is_array($this->elements)) {
			$this->elements = json_decode($this->elements, true);
		}
		return $result;
	}

	function link() {
		return Html_Tag::a("admin.php?page=consultation.php&consultations_id=".$this->id, $this->name);
	}

	function link_to_verifications() {
		return Html_Tag::a("admin.php?page=verifications.php&consultations_id=".$this->id, __("Verifications"));
	}

	function link_to_results() {
		return Html_Tag::a("admin.php?page=results.php&consultations_id=".$this->id, __("Results"));
	}

	function link_to_new() {
		return Html_Tag::a("admin.php?page=consultation.php&consultations_id=0", __("New consultation"));
	}
	
	function show_trouble_with_numbers() {
		$html = "<div class=\"consultation-error\">";
		$html .= __("The number of answers is different from the number of choices times the number of votes: you should not validate the results.");
		$html .= "</div>";
		
		return $html;
	}

	function show_trouble_with_hashes() {
		$html = "<div class=\"consultation-error\">";
		$html .= __("The hashes for this consultation, these votes and these answers are troublesome: you should not validate the results.");
		$html .= "</div>";
		
		return $html;
	}

	function show_verifications() {
		$votes = new Votes();
		$votes->consultations_id = $this->id;
		$votes->select();
		
		$answers = new Answers();
		$answers->consultations_id = $this->id;
		$answers->select();
		
		$html = "<h3>".__("Number of votes & answers")."</h3>";
		$html .= __("Choices = %s", array(count($this->elements['choices'])))."<br />";
		$html .= __("Votes = %s", array(count($votes)))."<br />";
		$html .= __("Answers = %s", array(count($answers)))."<br />";
		$html .= __("Choices * Votes = %s", array(count($this->elements['choices']) * count($votes)))."<br />";
		
		if (count($answers) != count($this->elements['choices']) * count($votes)) {
			$html .= $this->show_trouble_with_numbers();
		}
		
		$html .= "<h3>".__("Consultation's hashes")."</h3>";
		$html .= __("Consultation's hash = %s", array($this->hash))."<br />";
		$html .= __("Answers' hashes = %s", array(join(", ", $answers->consultations_hashes())))."<br />";
		$html .= __("Votes' hashes = %s", array(join(", ", $votes->consultations_hashes())))."<br />";
		
		$hashes = array_merge(array($this->hash), $answers->consultations_hashes(), $votes->consultations_hashes());
		$hashes = array_unique($hashes);
		if (count($hashes) > 1) {
			$html .= $this->show_trouble_with_hashes();
		}
		
		
		return $html;
	}

	function show_results() {
		$answers = new Answers();
		$answers->consultations_id = $this->id;
		$answers->select();
		
		$html = $answers->show_results();
		$html .= "<h3>".__("Results with Condorcet's method")."</h3>";
		$html .= $answers->show_results_with_condorcet_method();
		$html .= "<h3>".__("Results with Broda's method")."</h3>";
		$html .= $answers->show_results_with_broda_method();
		
		return $html;
	}
	
	function clean($variables) {
		$cleaned = array(
			'id' => isset($variables['id']) ? (int)$variables['id'] : 0,
			'name' => isset($variables['name']) ? $variables['name'] : "",
			'description' => isset($variables['description']) ? $variables['description'] : "",
			'comity_id' => isset($variables['comity_id']) ? (int)$variables['comity_id'] : 0,
			'elements' => array()
		);
		
		if (isset($variables['start'])) {
			$Y = isset($variables['start']['Y']) ? (int)$variables['start']['Y'] : 0;
			$m = isset($variables['start']['m']) ? (int)$variables['start']['m'] : 0;
			$d = isset($variables['start']['d']) ? (int)$variables['start']['d'] : 0;
			$cleaned['start'] = mktime(0, 0, 0, $m, $d, $Y);
		}
		if (isset($variables['stop'])) {
			$Y = isset($variables['stop']['Y']) ? (int)$variables['stop']['Y'] : 0;
			$m = isset($variables['stop']['m']) ? (int)$variables['stop']['m'] : 0;
			$d = isset($variables['stop']['d']) ? (int)$variables['stop']['d'] : 0;
			$cleaned['stop'] = mktime(0, 0, 0, $m, $d, $Y);
		}
		
		$cleaned['elements']['preambules'] = array();
		if (isset($variables['elements']['preambules']) and is_array($variables['elements']['preambules'])) {
			foreach ($variables['elements']['preambules'] as $preambule) {
				if (isset($preambule['url']) and !empty($preambule['url']) and isset($preambule['title']) and !empty($preambule['title'])) {
					$cleaned['elements']['preambules'][] = array('url' => $preambule['url'], 'title' => $preambule['title']);
				}
			}
		}
		
		$cleaned['elements']['opinions'] = array();
		if (isset($variables['elements']['opinions']) and is_array($variables['elements']['opinions'])) {
			foreach ($variables['elements']['opinions'] as $preambule) {
				if (isset($preambule['url']) and !empty($preambule['url']) and isset($preambule['title']) and !empty($preambule['title'])) {
					$cleaned['elements']['opinions'][] = array('url' => $preambule['url'], 'title' => $preambule['title']);
				}
			}
		}
		
		if (isset($variables['elements']['question'])) {
			$cleaned['elements']['question'] = $variables['elements']['question'];
		} else {
			$cleaned['elements']['question'] = "";
		}
		
		$cleaned['elements']['choices'] = array();
		if (isset($variables['elements']['choices']) and is_array($variables['elements']['choices'])) {
			foreach ($variables['elements']['choices'] as $preambule) {
				if (isset($preambule['tag']) and !empty($preambule['tag']) and isset($preambule['description']) and !empty($preambule['description'])) {
					$cleaned['elements']['choices'][] = array('tag' => $preambule['tag'], 'description' => $preambule['description']);
				}
			}
		}
		
		return $cleaned;
	} 
	
	function edit() {
		$html = "";
		
		if ($this->is_started()) {
			$html .= $this->show_started_sign();
		}
		
		$html .= "<form method=\"post\" action=\"\">";
		
		$id = new Html_Input("consultation[id]", $this->id);
		$html .= $id->input_hidden();
		
		$comities = new Comities(new Db($GLOBALS['bigficheconfig']));
		$comities->select();

		$html .= "<fieldset>";
		$html .= "<legend>".__("Presentation")."</legend>";
		$name = new Html_Input("consultation[name]", $this->name);
		$html .= $name->paragraph(__("Name"));
		$description = new Html_Textarea("consultation[description]", $this->description);
		$html .= $description->paragraph(__("Description"));
		$comity_id = new Html_Select("consultation[comity_id]", array('--' => "--") + $comities->names(), $this->comity_id);
		$html .= $comity_id->paragraph(__("Comity"));
		$html .= "</fieldset>";
		
		$html .= "<fieldset>";
		$html .= "<legend>".__("Period")."</legend>";
		$start = new Html_Input_Date("consultation[start]", $this->start > 0 ? $this->start : "");
		$html .= $start->paragraph(__("Start"));
		$stop = new Html_Input_Date("consultation[stop]", $this->stop > 0 ? $this->stop : "");
		$html .= $stop->paragraph(__("Stop"));
		$html .= "</fieldset>";
		
		$html .= "<fieldset>";
		$html .= "<legend>".__("Preambule : list of facts")."</legend>";
		if (isset($this->elements['preambules']) and is_array($this->elements['preambules'])) {
			$preambules = $this->elements['preambules'];
		}
		$preambules[] = array('url' => "", 'title' => "");
		if (count($preambules) == 1) {
			$preambules[] = array('url' => "", 'title' => "");
			$preambules[] = array('url' => "", 'title' => "");
		}
		foreach ($preambules as $i => $preambule) {
			$i++;
			$url = new Html_Input("consultation[elements][preambules][".$i."][url]", $preambule['url']);
			$url->placeholder = "URL";
			$url->class = "consultation-url";
			$title = new Html_Input("consultation[elements][preambules][".$i."][title]", $preambule['title']);
			$title->placeholder = __("Title");
			$title->class = "consultation-title";
			$html .= "<p>".$url->input()." ".$title->input()."</p>";
		}
		$html .= "</html>";
		
		$html .= "<fieldset>";
		$html .= "<legend>".__("Opinions : list of views")."</legend>";
		$opinions = array();
		if (isset($this->elements['opinions']) and is_array($this->elements['opinions'])) {
			$opinions = $this->elements['opinions'];
		}
		$opinions[] = array('url' => "", 'title' => "");
		if (count($opinions) == 1) {
			$opinions[] = array('url' => "", 'title' => "");
			$opinions[] = array('url' => "", 'title' => "");
		}
		foreach ($opinions as $i => $opinion) {
			$url = new Html_Input("consultation[elements][opinions][".$i."][url]", $opinion['url']);
			$url->placeholder = "URL";
			$url->class = "consultation-url";
			$title = new Html_Input("consultation[elements][opinions][".$i."][title]", $opinion['title']);
			$title->placeholder = __("Title");
			$title->class = "consultation-title";
			$html .= "<p>".$url->input()." ".$title->input()."</p>";
		}
		$html .= "</html>";
		
		$html .= "<fieldset>";
		$html .= "<legend>".__("Question")."</legend>";
		$question = new Html_Textarea("consultation[elements][question]", isset($this->elements['question']) ? $this->elements['question'] : "");
		$html .= $question->input();
		$html .= "</fieldset>";
		
		$html .= "<fieldset>";
		$html .= "<legend>".__("Choices")."</legend>";
		$choices = array();
		if (isset($this->elements['choices']) and is_array($this->elements['choices'])) {
			$choices = $this->elements['choices'];
		}
		$choices[] = array('tag' => "", 'description' => "");
		if (count($choices) == 1) {
			$choices[] = array('tag' => "", 'description' => "");
			$choices[] = array('tag' => "", 'description' => "");
		}
		foreach ($choices as $i => $choice) {
			$tag = new Html_Input("consultation[elements][choices][".$i."][tag]", $choice['tag']);
			$tag->placeholder = "Tag";
			$tag->class = "consultation-tag";
			$description = new Html_Input("consultation[elements][choices][".$i."][description]", $choice['description']);
			$description->placeholder = __("Description");
			$description->class = "consultation-description";
			$html .= "<p>".$tag->input()." ".$description->input()."</p>";
		}
		
		$vote = new Html_Input("save", __("Save"), "submit");
		$vote->properties['class'] = "btn btn-success consultation-submit";
		$html .= $vote->input();
		
		$html .= "</form>";
		
		return $html;
	}
	
	function name() {
		if (!empty($this->filename)) {
			return preg_replace("/^.*\/([a-zA-Z0-9-]*)\.php$/", "//1", $this->filename);
		} else {
			return "";
		}
	}
	
	function charge_from_variables($variables) {
		if (isset($variables['elements'])) {
			$this->elements = $variables['elements'];
			return true;
		} else {
			return false;
		}
	}

	function charge_from_name($name) {
		$filename = __DIR__."/../configuration/elements/".$name.".php";
		return $this->charge($filename);
	}

	function charge($filename) {
		if (file_exists($filename)) {
			$this->filename = $filename;
			require $filename;
			$this->elements = $elements;
			return true;
		} else {
			return false;
		}
		
	}
	
	function charge_next() {
		require __DIR__."/../configuration/elements/201406-groupe-parlementaire.php";
		$this->elements = $elements;
	}
	
	function hash() {
		return md5(serialize($this->elements));
	}
	
	function is_started() {
		if (isset($this->start) and $this->start < time()) {
			return true;
		} else {
			return false;
		}
	}
	
	function is_open() {
		if (!isset($this->start) or !isset($this->stop)) {
			return false;
		}

		if ($this->start <= time() and time() <= $this->stop) {
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
		foreach ($this->elements['preambules'] as $preambule) {
			$html .= "<li>".Html_Tag::a($preambule['url'], $preambule['title'], array('target' => "_blank"))."</li>";
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
		foreach ($this->elements['opinions'] as $opinion) {
			$html .= "<li>".Html_Tag::a($opinion['url'], $opinion['title'], array('target' => "_blank"))."</li>";
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
		foreach ($this->elements['choices'] as $choice) {
			$html .= "<tr>";
			$html .= "<td>".$choice['description']."</td>";
			for ($i = 1; $i <= $i_max; $i++) {
				$choice_for_answers = new Html_Input("consultation[answers][".$choice['tag']."]", $i, "radio");
				$html .= "<td>".$choice_for_answers->input()."</td>";
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
		if ($vote->match_existing(array("members_id", "consultations_hash"))) {
			$vote->load();
		}
		if ($vote->is_done()) {
			$vote->load();
			$html = "<div class=\"consultation-information\">";
			$html .= __("Your answers were found : you'll find them below.");
			$html .= "</div>";
			
			$answers = new Answers();
			$answers->consultations_hash = $this->hash();
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
		$html .= "<p>".__("This consultation will be closed definitely on %s.", array(date("d/m/Y H:i", $this->stop)))."</p>";
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
		
		$tags = array();
		foreach ($this->elements['choices'] as $choice) {
			$tags[] = $choice['tag'];
		}
		switch (true) {
			case array_diff(array_keys($answers), $tags) != array():
			case array_diff($values, array_values($answers)) != array(): 
				return false;
			default:
				return true;
		}
	}
	
	function show_started_sign() {
		$html = "<div class=\"consultation-started\">";
		$html .= __("This consultation has already started.");
		$html .= "</div>";
		
		return $html;
	}

	function show_closed_sign() {
		$html = "<div class=\"consultation-error\">";
		$html .= __("No consultation is currently open.");
		$html .= "</div>";
		
		return $html;
	}
	
	function show_problem_with_save() {
		$html = "<div class=\"consultation-error\">";
		$html .= __("Your answers were not saved. Please contact the technical team : %s.", array($GLOBALS['param']['preparation_email']));
		$html .= "</div>";
		
		return $html;
	}
	
	function show_problem_with_answers() {
		$html = "<div class=\"consultation-error\">";
		$html .= __("Your answers are not coherent. Please make sure you're ranking all the possibles choices.");
		$html .= "</div>";
		
		return $html;
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
		$result = $this->db->id("
			INSERT INTO consultations
			SET name = ".$this->db->quote($this->name).",
			description = ".$this->db->quote($this->description).",
			comity_id = ".(int)$this->comity_id.",
			start = ".(int)$this->start.",
			stop = ".(int)$this->stop.",
			elements = ".$this->db->quote(json_encode($this->elements)).",
			hash = ".$this->db->quote($this->hash()).",
			time = ".time()
		);
		$this->id = $result[2];
		$this->db->status($result[1], "i", __("consultation"));

		return $this->id;
	}
	
	function update() {
		$result = $this->db->query("
			UPDATE consultations
			SET name = ".$this->db->quote($this->name).",
			description = ".$this->db->quote($this->description).",
			comity_id = ".(int)$this->comity_id.",
			start = ".(int)$this->start.",
			stop = ".(int)$this->stop.",
			elements = ".$this->db->quote(json_encode($this->elements)).",
			hash = ".$this->db->quote($this->hash()).",
			time = ".time()."
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "u", __("consultation"));

		return $this->id;
	}

	function delete() {
		$result = $this->db->query("
			DELETE FROM consultations
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "d", __("consultation"));

		return $this->id;
	}
	
	function is_deletable() {
		return true;
	}
}
