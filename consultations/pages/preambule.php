<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$consultation = new Consultation();
$consultation->charge_next();
if ($consultation->is_open()) {
	echo $consultation->show_preambule();
} else {
	echo $consultation->show_closed_sign();
}
