<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Comities extends Collector {
	function __construct(Db $db = null) {
		parent::__construct("Comity", "comities", $db);
	}
	
	function names() {
		$names = array();
		foreach ($this as $comity) {
			$names[$comity->id] = $comity->name;
		}
		return $names;
	}
}
