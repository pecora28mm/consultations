<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$_SESSION['consultation']['members_id'] = isset($_GET['members_id']) ? $_GET['members_id'] : (isset($_SESSION['consultation']['members_id']) ? $_SESSION['consultation']['members_id'] : 1);
$_SESSION['consultation']['consultations_id'] = isset($_GET['consultations_id']) ? $_GET['consultations_id'] : (isset($_SESSION['consultation']['consultations_id']) ? $_SESSION['consultation']['consultations_id'] : 3);

$consultation = new Consultation();
$consultation->load($_SESSION['consultation']['consultations_id']);
if ($consultation->is_open()) {
	echo $consultation->show_opened_sign();
	
	$vote = new Vote();
	$vote->charge($consultation, new Member($_SESSION['consultation']['members_id']));
	if ($vote->match_existing(array("members_id", "consultations_hash"))) {
		$vote->load();
	}
	if ($vote->is_done()) {
		echo $vote->show_summary();
	}

	echo $consultation->show_procedure();
} else {
	echo $consultation->show_closed_sign();
}
