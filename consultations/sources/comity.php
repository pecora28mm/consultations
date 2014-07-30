<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Comity extends Record {
	public $id = 0;
	public $name = "";

	function __construct($id = 0, db $db = null) {
		parent::__construct($db);
		$this->id = $id;
	}
}
