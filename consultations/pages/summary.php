<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$consultation = new Consultation();
$consultation->charge_next();
if ($consultation->is_open()) {
	echo $consultation->show_summary(new Member($_SESSION['consultation']['members_id']));
} else {
	echo $consultation->show_closed_sign();
}
