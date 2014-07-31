<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

require __DIR__."/../configuration/configuration.php";
require __DIR__."/../configuration/fr_FR.php";

require __DIR__."/collector.php";
require __DIR__."/db.php";
require __DIR__."/debug.php";
require __DIR__."/misc.php";
require __DIR__."/record.php";

require __DIR__."/answer.php";
require __DIR__."/answers.php";
require __DIR__."/bot.php";
require __DIR__."/comity.php";
require __DIR__."/comities.php";
require __DIR__."/config_file.php";
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
require __DIR__."/member_comity.php";
require __DIR__."/members.php";
require __DIR__."/members_comities.php";
require __DIR__."/preparation.php";
require __DIR__."/template.php";
require __DIR__."/template_admin.php";
require __DIR__."/vote.php";
require __DIR__."/votes.php";
require __DIR__."/update.php";

if (function_exists("date_default_timezone_set")) {
	date_default_timezone_set($GLOBALS['param']['locale_timezone']);
}
