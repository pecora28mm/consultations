<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$elements = array(
	'period' => array(
		'start' => mktime(0, 0, 0, 5, 1, 2014),
		'stop' => mktime(0, 0, 0, 6, 1, 2014),
	),
	'preambules' => array(
		array(
			'url' => "http://www.eppgroup.eu/",
			'title' => "Groupe du Parti populaire européen - PPE",
		),
		array(
			'url' => "http://www.socialistsanddemocrats.eu/",
			'title' => "Alliance progressiste des socialistes et des démocrates - S&D",
		),
		array(
			'url' => "http://www.alde.eu/",
			'title' => "Groupe Alliance des démocrates et des libéraux pour l'Europe - ADLE",
		),
		array(
			'url' => "http://www.greens2014.eu/",
			'title' => "Groupe des Verts/Alliance libre européenne - Verts/ALE",
		),
		array(
			'url' => "http://www.ecrgroup.eu/",
			'title' => "Groupe des conservateurs et des réformateurs européens - ECR",
		),
		array(
			'url' => "http://www.guengl.eu/",
			'title' => "Gauche unitaire européenne/Gauche verte nordique - GUE/NGL",
		),
		array(
			'url' => "http://www.efdgroup.eu/",
			'title' => "Groupe Europe libertés démocratie - ELD",
		),
	),
	'opinions' => array(
		array(
			'url' => "https://fr.wikipedia.org/wiki/Nouvelle_Donne_(parti_politique)",
			'title' => "Nouvelle Donne (parti politique) sur Wikipedia",
		),
		array(
			'url' => "http://www.nouvelledonne.fr/communiques/nouvelle-donne-au-parlement-europeen-avec-quel-groupe",
			'title' => "Nouvelle Donne au Parlement européen : avec quel groupe ? (communiqué)",
		),
		array(
			'url' => "http://www.nouvelledonne.fr/comites-locaux",
			'title' => "L'opinion des comités locaux",
		),
		array(
			'url' => "https://www.google.fr/#q=Quel+groupe+parlementaire+doit+rejoindre+Nouvelle+Donne+au+Parlement+Europ%C3%A9en+%3F",
			'title' => "L'opinion de Google",
		),
	),
	'question' => "Quel groupe parlementaire doit rejoindre Nouvelle Donne au Parlement Européen ?",
	'choices' => array(
		array(
			'tag' => "PSE",
			'value' => "Alliance progressiste des socialistes et des démocrates - PSE",
		),
		array(
			'tag' => "PVE",
			'value' => "Groupe des Verts/Alliance libre européenne - PVE",
		),
		array(
			'tag' => "PGE",
			'value' => "Gauche unitaire européenne/Gauche verte nordique - PGE",
		),
		array(
			'tag' => "000",
			'value' => "Non-inscrits - NI",
		),
	),
);
