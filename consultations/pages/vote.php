<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$consultation = new Consultation();
$consultation->charge_next();

$answers = array();
if (isset($_POST['consultation'])) {
	$answers = $consultation->clean_answers(isset($_POST['consultation']['answers']) ? $_POST['consultation']['answers'] : array());
	if ($consultation->are_answers_coherent($answers)) {
		$vote = new Vote();
		$vote->charge($consultation, new Member($_SESSION['consultation']['members_id']), $answers, time());
		if ($vote->match_existing(array("consultation_hash", "members_id"))) {
			$vote->load();
		}
		$vote->save_with_answers();
		if ($vote->is_done()) {
			header("Location: ".$consultation->url_to_summary());
		} else {
			echo $consultation->show_problem_with_save();
		}
		
	} else {
		echo $consultation->show_problem_with_answers();
	}
}

if ($consultation->is_open()) {
	echo $consultation->show_vote($answers);
} else {
	echo $consultation->show_closed_sign();
}
