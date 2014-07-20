<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$consultation_id = isset($_GET['consultations_id']) ? (int)$_GET['consultations_id'] : 0;

$consultation = new Consultation();
$consultation->load($consultation_id);

echo "<h2>".__("Show the results for the consultation '%s'", array($consultation->name))."</h2>";
echo $consultation->show_results();
