<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

require __DIR__."/../configuration/configuration.php";
require __DIR__."/../configuration/fr_FR.php";

require __DIR__."/collector.php";
require __DIR__."/db.php";
require __DIR__."/misc.php";
require __DIR__."/record.php";

require __DIR__."/answer.php";
require __DIR__."/answers.php";
require __DIR__."/consultation.php";
require __DIR__."/consultations.php";
require __DIR__."/html_checkbox.php";
require __DIR__."/html_input.php";
require __DIR__."/html_input_ajax.php";
require __DIR__."/html_input_date.php";
require __DIR__."/html_radio.php";
require __DIR__."/html_select.php";
require __DIR__."/html_select_ajax.php";
require __DIR__."/html_tag.php";
require __DIR__."/html_textarea.php";
require __DIR__."/member.php";
require __DIR__."/template.php";
require __DIR__."/template_admin.php";
require __DIR__."/vote.php";

if (function_exists("date_default_timezone_set")) {
	date_default_timezone_set($GLOBALS['param']['locale_timezone']);
}
