<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

require __DIR__."/../sources/bootloader.php";

$bot = new Bot();
$res = $bot->execute(isset($argv) ? $argv : null, $_GET);
if (is_bool($res)) {
	return $res;
}
else {
	echo $res;
}

