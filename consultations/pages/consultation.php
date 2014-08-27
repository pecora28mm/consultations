<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$token = isset($_GET['token']) ? $_GET['token'] : "";

$consultation = new Consultation();
$consultation->token = $token;
if (!empty($token) and $consultation->match_existing(array("token"))) {
	$consultation->load();
}
$consultation_id = $consultation->id;

if (isset($_POST['consultation'])) {
	$consultation = new Consultation();
	$cleaned = $consultation->clean($_POST['consultation']);
	$consultation->load($cleaned['id']);
	$consultation->fill($cleaned);
	$consultation->save();
	$consultation_id = $consultation->id; 
}

$consultation = new Consultation();
$consultation->load($consultation_id);

if ($consultation->is_closed()) {
	echo "<h2>".__("Show the results for the consultation '%s'", array($consultation->name))."</h2>";
	echo $consultation->show_results();
	echo "<h2>".__("Show the verifications for the consultation '%s'", array($consultation->name))."</h2>";
	echo $consultation->show_verifications();
	
} elseif ($consultation->is_open()) {
	echo "<h2>".__("Show the consultation '%s'", array($consultation->name))."</h2>";
	echo $consultation->show();
	
} elseif ($consultation->id > 0) {
	echo "<h2>".__("Edit the consultation '%s'", array($consultation->name))."</h2>";
	echo $consultation->edit();
	echo $consultation->help_sending_convocations();
	
} else {
	echo "<h2>".__("Create a new consultation")."</h2>";
	echo $consultation->edit();
}
