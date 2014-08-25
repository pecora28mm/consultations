<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Update {
	public $config;
	public $param;

	function __construct(db $db = null) {
		if ($db === null) {
			$db = new db();
		}
		$this->db = $db;
		$this->config = new Config_File(dirname(__FILE__)."/../configuration/configuration.php", "config");
		$this->param = new Config_File(dirname(__FILE__)."/../configuration/configuration.php", "param");
		$this->dbconfig = new Config_File(dirname(__FILE__)."/../configuration/configuration.php", "dbconfig");
		$this->bigficheconfig = new Config_File(dirname(__FILE__)."/../configuration/configuration.php", "bigficheconfig");
	}
	
	function to_4() {
		$this->db->query("ALTER TABLE `consultations` ADD `email` MEDIUMTEXT NOT NULL DEFAULT '' AFTER `description`;");
	}

	function to_3() {
		$this->db->query("ALTER TABLE `consultations` ADD `emails` MEDIUMTEXT NOT NULL DEFAULT '' AFTER `comity_id`;");
	}

	function to_2() {
		$this->db->query("ALTER TABLE `consultations` ADD `token` VARCHAR(255) NOT NULL DEFAULT '' AFTER `id`;");
	}

	function to_1() {
		$this->config->add("version", 0);
	}

	function current() {
		$values = $this->config->values();
		return $values['config']['version'];
	}

	function last() {
		$last = 0;
		$methods = get_class_methods($this);
		foreach ($methods as $method) {
			if (preg_match("/^to_[0-9]*$/", $method)) {
				$last = max($last, (int)substr($method, 3));
			}
		}
		return $last;
	}

	function config($key, $value) {
		$values = array('config' => $this->config->values());
		$values['config']['config'][$key] = $value;

		return $this->config->update($values);
	}
}
