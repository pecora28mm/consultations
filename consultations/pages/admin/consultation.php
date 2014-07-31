<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$consultation_id = isset($_GET['consultations_id']) ? (int)$_GET['consultations_id'] : 0;

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

echo "<h2>".__("Edit the consultation '%s'", array($consultation->name))."</h2>";
echo $consultation->edit();
echo $consultation->help_sending_convocations();
