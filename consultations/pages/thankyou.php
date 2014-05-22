<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$consultation = new Consultation();
$consultation->charge_next();
if ($consultation->is_open()) {
// $consultation->thank(new Member($_SESSION['consultation']['members_id']));
	echo $consultation->show_thankyou();
} else {
	echo $consultation->show_closed_sign();
}

