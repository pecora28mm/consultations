<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$token = isset($_GET['token']) ? $_GET['token'] : (isset($_SESSION['consultation']['consultations_token']) ? $_SESSION['consultation']['consultations_token'] : "");

$consultation = new Consultation();
$consultation->token = $token;  
if (!empty($token) and $consultation->match_existing(array("token"))) {
	$consultation->load();
	$_SESSION['consultation']['consultations_token'] = $token;

	echo "<h2>".__("Show the results for the consultation '%s'", array($consultation->name))."</h2>";
	if ($consultation->is_closed()) {
		echo $consultation->show_results();
	} else {
		echo $consultation->show_not_closed_sign();
	}
	echo $consultation->show_link_to_public();

} else {
	echo $consultation->show_results();
}
