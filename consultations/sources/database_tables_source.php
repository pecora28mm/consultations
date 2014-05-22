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
				  consultation_hash MEDIUMTEXT NOT NULL DEFAULT '',
				  choice MEDIUMTEXT NOT NULL DEFAULT '',
				  position INT(11) DEFAULT NULL,
				  time INT(10) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
			),
			'votes' => array(
				"CREATE TABLE votes (
				  id INT(21) NOT NULL AUTO_INCREMENT,
				  members_id INT(21) NOT NULL DEFAULT '0',
				  consultation_hash MEDIUMTEXT NOT NULL DEFAULT '',
				  day INT(10) NOT NULL DEFAULT '0',
				  hash MEDIUMTEXT NOT NULL DEFAULT '',
				  time INT(10) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
			),
		);
	}
}
