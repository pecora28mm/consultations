<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$consultation = new Consultation();
$consultation->load($_SESSION['consultation']['consultations_id']);
if ($consultation->is_open()) {
	echo $consultation->show_summary(new Member($_SESSION['consultation']['members_id']));
} else {
	echo $consultation->show_closed_sign();
}
