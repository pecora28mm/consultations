<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$consultation = new Consultation();
$consultation->load($_SESSION['consultation']['consultations_id']);
if ($consultation->is_open()) {
	echo $consultation->show_preambule();
} else {
	echo $consultation->show_closed_sign();
}
