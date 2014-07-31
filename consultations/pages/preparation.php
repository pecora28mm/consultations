<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$preparation = new Preparation();

if (isset($_POST['preparation']['email'])) {
	$bigfichedb = new Db($GLOBALS['bigficheconfig']);
	
	if ($preparation->charge_member_with_email($_POST['preparation']['email'], $bigfichedb)) {
		$preparation->charge_open_consultations_for_member();
		if ($preparation->is_ready()) {
			if ($preparation->send_convocations_for_member()) {
				echo $preparation->show_convocations_sent();
			} else {
				echo $preparation->show_error_while_sending_convocations();
			}
		} else {
			echo $preparation->show_no_open_consultation();
		}
	} else {
		echo $preparation->show_email_not_valid();
	}
}

echo $preparation->ask_for_email();
