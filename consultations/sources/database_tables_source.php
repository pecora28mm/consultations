<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Database_Tables_Source {
	protected $db;

	function __construct($db = null) {
		if ($db == null) {
			$db = new db();
		}
		$this->db = $db;
	}

	function enumerate() {
		return array(
			'answers' => array(
				"CREATE TABLE answers (
				  id INT(21) NOT NULL AUTO_INCREMENT,
				  members_id INT(21) NOT NULL DEFAULT '0',
				  consultations_id INT(11) NOT NULL DEFAULT '0',
				  consultations_hash MEDIUMTEXT NOT NULL DEFAULT '',
				  choice MEDIUMTEXT NOT NULL DEFAULT '',
				  position INT(11) DEFAULT NULL,
				  time INT(10) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
			),
			'consultations' => array(
				"CREATE TABLE consultations (
				  id INT(21) NOT NULL AUTO_INCREMENT,
				  token VARCHAR(255) NOT NULL DEFAULT '',
				  name MEDIUMTEXT NOT NULL DEFAULT '',
				  description MEDIUMTEXT NOT NULL DEFAULT '',
				  email MEDIUMTEXT NOT NULL DEFAULT '',
				  comity_id INT(11) NOT NULL DEFAULT '0',
				  emails MEDIUMTEXT NOT NULL DEFAULT '',
				  everyone TINYINT(4) NOT NULL DEFAULT '0',
				  start INT(10) NOT NULL DEFAULT '0',
				  stop INT(10) NOT NULL DEFAULT '0',
				  elements MEDIUMTEXT NOT NULL DEFAULT '',
				  hash MEDIUMTEXT NOT NULL DEFAULT '',
				  time INT(10) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
			),
			'members' => array(
				"CREATE TABLE `members` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `sexe` enum('?','M','F') NOT NULL DEFAULT '?',
				  `prenom` varchar(100) NOT NULL,
				  `nom` varchar(100) NOT NULL,
				  `nom_std` varchar(100) NOT NULL,
				  `email` varchar(255) NOT NULL,
				  `adresse1` varchar(100) NOT NULL,
				  `adresse2` varchar(100) NOT NULL,
				  `codepostal` varchar(10) NOT NULL,
				  `pays_id` int(11) unsigned NOT NULL,
				  `commune_id` int(11) unsigned NOT NULL,
				  `commune_etranger` varchar(50) NOT NULL,
				  `tel` varchar(20) NOT NULL,
				  `mob` varchar(20) NOT NULL,
				  `created` datetime NOT NULL,
				  `modified` datetime NOT NULL,
				  `active` tinyint(1) NOT NULL DEFAULT '1',
				  `mail_optin` tinyint(4) NOT NULL,
				  `mail_sync_status` tinyint(4) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
			),
			'members_comities' => array(
				"CREATE TABLE `members_comities` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `member_id` int(11) DEFAULT NULL,
				  `comity_id` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
			),
			'votes' => array(
				"CREATE TABLE votes (
				  id INT(21) NOT NULL AUTO_INCREMENT,
				  members_id INT(21) NOT NULL DEFAULT '0',
				  consultations_id INT(11) NOT NULL DEFAULT '0',
				  consultations_hash MEDIUMTEXT NOT NULL DEFAULT '',
				  day INT(10) NOT NULL DEFAULT '0',
				  hash MEDIUMTEXT NOT NULL DEFAULT '',
				  time INT(10) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
			),
		);
	}
}
