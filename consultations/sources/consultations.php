<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Consultations extends Collector {
	function __construct(Db $db = null) {
		parent::__construct("Consultation", "consultations", $db);
	}
	
	function select($raw = false) {
		parent::select($raw);
		foreach ($this as $id => $consultation) {
			$this[$id]->elements = json_decode($consultation->elements, true);
		}
	}
	
	function manage() {
		$html = "";
		
		if (count($this) > 0) {
			$html .= "<div class=\"consultations-manage\">";
			foreach ($this as $consultation) {
				$html .= "<div class=\"consultation-manage\">";
				$html .= "<div class=\"consultation-edit\">".$consultation->link()."</div>";
				$html .= "<div class=\"consultation-time\">".date("d/m/Y", $consultation->time)."</div>";
				$html .= "<div class=\"consultation-results\">".$consultation->link_to_results()."</div>";
				$html .= "<div class=\"consultation-verifications\">".$consultation->link_to_verifications()."</div>";
				$html .= "</div>";
			}
			$html .= "</div>";
		}
		
		return $html;
	}

	function get_where() {
		$where = parent::get_where();
		
		if (isset($this->day)) {
			$where[] = "consultations.start <= ".(int)$this->day;
			$where[] = "consultations.stop >= ".(int)$this->day;
		}
		
		return $where;
	}
}
