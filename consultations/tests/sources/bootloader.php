<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

require_once __DIR__."/../../sources/bootloader.php";
require_once __DIR__."/../../sources/database_tables.php";
require_once __DIR__."/../../sources/database_tables_source.php";
require_once __DIR__."/../libraries/simpletest/autorun.php";
require_once __DIR__."/simpletest_table_tester.php";

session_start();

$GLOBALS['dbconfig']['name'] = "dvlpt_test";

$db = new db();
if (!$db->database_exists($GLOBALS['dbconfig']['name'])) {
	$db->query("CREATE SCHEMA `".$GLOBALS['dbconfig']['name']."`");
	if (!$db->database_exists($GLOBALS['dbconfig']['name'])) {
		echo "<br />"."Access denied"."\n";
		exit();
	}
}
