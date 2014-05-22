<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$elements = array(
	'period' => array(
		'start' => mktime(0, 0, 0, 5, 1, 2014),
		'stop' => mktime(0, 0, 0, 6, 1, 2014),
	),
	'preambules' => array(
		'http://www.pes.eu/' => "Parti Socialiste Européen - PSE",
		'http://www.europeangreens.eu/' => "Parti vert européen - PVE",
		'http://www.european-left.org/' => "Parti de la gauche européenne - PGE",
	),
	'opinions' => array(
		'http://onpk.net/' => "Billet de blog de Perrick",
		'http://liberation.fr/' => "Article sur Liberation",
		'http://lemonde.fr/' => "Article sur Le Monde",
	),
	'question' => "Quel groupe parlementaire doit rejoindre Nouvelle Donne au Parlement Européen ?",
	'choices' => array(
		'PSE' => "Parti Socialiste Européen - PSE",
		'PVE' => "Parti vert européen - PVE",
		'PGE' => "Parti de la gauche européenne - PGE",
		'000' => "Aucun groupe",
	),
);
