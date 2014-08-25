<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$preparation = new Preparation();
if (isset($_GET['key'])) {
	$bigfichedb = new Db($GLOBALS['bigficheconfig']);
	if ($preparation->charge_member_and_consultation_with_key($_GET['key'], $bigfichedb)) {
		$_SESSION['consultation']['members_id'] = $preparation->member_id();
		$_SESSION['consultation']['consultations_id'] = $preparation->consultation_id();
		header("Location: ".$preparation->url_to_procedure());
	} else {
		echo $preparation->show_wrong_convocation();
	}
} else {
	echo $preparation->show_link_asking_for_email();
}

$consultation = new Consultation();
echo $consultation->show_link_to_public_new();
