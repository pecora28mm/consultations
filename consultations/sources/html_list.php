<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Html_List {
	public $grid = array();

	function __construct($grid) {
		$this->grid = $grid;
		$this->normalize();
	}

	function normalize() {
		if (!is_array($this->grid)) {
			$this->grid = array('leaves' => array());
		}
		if (!isset($this->grid['leaves'])) {
			$grid['leaves'] = $this->grid;
			$this->grid = $grid;
		}
	}
	
	function show() {
		$html = "";
		
		if (count($this->grid['leaves']) > 0) {
			$attributes = "";
			foreach ($this->grid as $element => $value) {
				if ($element != "leaves") {
					$attributes .= " ".$element."=\"".$value."\"";
				}
			}
			$html = "<ul".$attributes.">";
			
			foreach ($this->grid['leaves'] as $leaf) {
				$attributes = "";
				foreach ($leaf as $element => $value) {
					if ($element != "value") {
						$attributes .= " ".$element."=\"".$value."\"";
					}
				}
				$html .= "<li".$attributes.">".$leaf['value']."</li>";
			}
		
			$html .= "</ul>";
		}

		return $html;
	}
	
	function item($name, $value) {
		return "<label>".$name."</label><div>".$value."</div>";
	}
}
