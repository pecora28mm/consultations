<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Database_Tables {
	public $elements = array();

	protected $db;

	function __construct($db = null) {
		if ($db == null) {
			$db = new Db();
		}
		$this->db = $db;
	}

	function sources() {
		$sources = array(
			new Database_Tables_Source($this->db)
		);

		return $sources;
	}

	function prepare() {
		foreach ($this->sources() as $source) {
			$this->elements = array_merge_recursive($this->elements, $source->enumerate());
		}
	}

	function install($table = null) {
		$queries = null;
		if ($table == null) {
			$queries = $this->elements;
		} else if (isset($this->elements[$table])) {
			$queries = $this->elements[$table];
		}

		if ($queries != null) {
			$this->db->initialize($queries);
		}
	}
}
