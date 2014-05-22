<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Answers extends Collector {
	function __construct(Db $db = null) {
		parent::__construct("Answer", "answers", $db);
	}

	function get_where() {
		$where = parent::get_where();
		
		if (isset($this->actions_id)) {
			$where[] = "results.actions_id = ".(int)$this->actions_id;
		}
		
		return $where;
	}
	
	function delete() {
		foreach ($this as $answer) {
			$answer->delete();
		}
	}
}
