<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$_SESSION['consultation']['members_id'] = isset($_SESSION['consultation']['members_id']) ? $_SESSION['consultation']['members_id'] : 1;

$consultation = new Consultation();
$consultation->charge_next();
if ($consultation->is_open()) {
	echo $consultation->show_procedure();
} else {
	echo $consultation->show_closed_sign();
}
