<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Answers extends Collector {
	function __construct(Db $db = null) {
		parent::__construct("Answer", "answers", $db);
	}
	
	function consultations_hashes() {
		$consultations_hashes = array();
		foreach ($this as $vote) {
			$consultations_hashes[] = $vote->consultations_hash;
		}
		return array_unique($consultations_hashes);
	}

	function show_rankings($results) {
		$html = "<ol>";
		$i = 0;
		$points_last = -1;
		foreach ($results as $tag => $points) {
			if ($points != $points_last) {
				$i++;
			}
			$html .= "<li>".__("Position %s: %s with %s points", array($i, $tag, $points))."</li>";
			if ($points == $points_last) {
				$i++;
			}
			$points_last = $points;
		}
		$html .= "</ol>";
		
		return $html;
	}
	
	function show_results_with_condorcet_method() {
		foreach ($this as $answer) {
			$partials[$answer->members_id][$answer->position] = $answer->choice;
		}
		
		foreach ($partials as $members_id => $values) {
			ksort($values);
			$partials[$members_id] = $values;
		}
		
		foreach ($partials as $members_id => $values) {
			$choices_sorted = array_values($values);
			for ($i = 0; $i < count($choices_sorted); $i++) {
				for ($j = $i + 1; $j < count($choices_sorted); $j++) {
					if (!isset($duels[$choices_sorted[$i]][$choices_sorted[$j]])) {
						$duels[$choices_sorted[$i]][$choices_sorted[$j]] = 0;
					}
					$duels[$choices_sorted[$i]][$choices_sorted[$j]] += 1;
					if (!isset($duels[$choices_sorted[$j]][$choices_sorted[$i]])) {
						$duels[$choices_sorted[$j]][$choices_sorted[$i]] = 0;
					}
					$duels[$choices_sorted[$j]][$choices_sorted[$i]] -= 1;
				}
			}
		}
		
		foreach ($duels as $choice => $values) {
			$results[$choice] = array_sum($values);
		}
		
		arsort($results);
		
		return $this->show_rankings($results);
	}

	function show_results_with_broda_method() {
		foreach ($this as $answer) {
			if (!isset($results[$answer->choice])) {
				$results[$answer->choice] = 0;
			}
			$results[$answer->choice] += $answer->position;
		}
		
		asort($results);
		
		return $this->show_rankings($results);
	}

	function show_results() {
		foreach ($this as $answer) {
			if (!isset($results[$answer->position][$answer->choice])) {
				$results[$answer->position][$answer->choice] = 0;
			}
			$results[$answer->position][$answer->choice] += 1;
		}
		
		ksort($results);
		foreach ($results as $position => $values) {
			arsort($values);
			$results[$position] = $values;
		}
		
		$html = "<ol>";
		foreach ($results as $position => $values) {
			$html .= "<li>".__("Choice n°%s", array($position))."</li>";
			$html .= "<ol>";
			foreach ($values as $tag => $count) {
				$html .= "<li>".__("%s : %s votes", array($tag, $count))."</li>";
			}
			$html .= "</ol>";
		}
		$html .= "</ol>";
		
		return $html;
	}

	function get_where() {
		$where = parent::get_where();
		
		if (isset($this->members_id)) {
			$where[] = "answers.members_id = ".(int)$this->members_id;
		}
		if (isset($this->consultations_id)) {
			$where[] = "answers.consultations_id = ".$this->db->quote($this->consultations_id);
		}
		if (isset($this->consultations_hash)) {
			$where[] = "answers.consultations_hash = ".$this->db->quote($this->consultations_hash);
		}
		
		return $where;
	}
	
	function delete() {
		foreach ($this as $answer) {
			$answer->delete();
		}
	}
}
