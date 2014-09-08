<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Consultations extends Collector {
	function __construct(Db $db = null) {
		parent::__construct("Consultation", "consultations", $db);
	}
	
	function filter_out_by_comities_and_email($comity_ids, $email) {
		$instances = array();
		
		foreach ($this as $i => $consultation) {
			if ($consultation->comity_id > 0) {
				if (in_array($consultation->comity_id, $comity_ids)) {
					$instances[] = $consultation;
				}
			} else {
				$emails = explode(" ", $consultation->emails);
				if (in_array($email, $emails)) {
					$instances[] = $consultation;
				}
			}
		}
		
		$this->instances = $instances;
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
		
		if (isset($this->filters)) {
			$filters = array();
			if (isset($this->filters['comity_id'])) {
				if (is_array($this->filters['comity_id']) and count($this->filters['comity_id']) > 0) {
					$filters[] = "consultations.comity_id IN (".join(", ", $this->filters['comity_id']).")";
				} else {
					$filters[] = "consultations.comity_id = ".(int)$this->filters['comity_id'];
				}
			}
			if (isset($this->filters['emails'])) {
				$filters[] = "consultations.emails LIKE ('%".$this->filters['emails']."%')";
			}
			if (isset($this->filters['everyone'])) {
				$filters[] = "consultations.everyone = ".(int)$this->filters['everyone'];
			}
			if (count($filters) > 0) {
				$where[] = "(".join(" OR ", $filters).")";
			}
		}
		if (isset($this->comity_id)) {
			if (is_array($this->comity_id)) {
				$where[] = "consultations.comity_id IN (".join(", ", $this->comity_id).")";
			} else {
				$where[] = "consultations.comity_id = ".(int)$this->comity_id;
			}
		}
		if (isset($this->day)) {
			$where[] = "consultations.start <= ".(int)$this->day;
			$where[] = "consultations.stop >= ".(int)$this->day;
		}
		
		return $where;
	}
}
