<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

require __DIR__."/../sources/bootloader.php";

session_start();

$page = __DIR__."/../pages/home.php";
if (isset($_GET['page']) and preg_match("/^[a-z\.-]*\.php$/", $_GET['page'])) {
	$page = __DIR__."/../pages/".$_GET['page'];
	if (!file_exists($page)) {
		$page = __DIR__."/../pages/404.php";
	}
}

$template = new Template();
if (!isset($_GET['method']) or $_GET['method'] != "json") {
	echo $template->header();
	echo $template->navigation();
	echo $template->content_top();
}

require $page;

if (!isset($_GET['method']) or $_GET['method'] != "json") {
	echo $template->content_bottom();
	echo $template->footer();
}