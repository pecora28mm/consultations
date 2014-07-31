<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Bot {
	public $directory_configuration = "";

	function __construct(db $db = null) {
		if ($db === null) {
			$db = new db();
		}
		$this->db = $db;
		$this->directory_configuration = dirname(__FILE__)."/../configuration";
	}
	
	function send_convocations($params) {
		if (!isset($params['consultation']) or !is_numeric($params['consultation'])) {
			return __("For example: 'php bot.php --send_convocations consultation=X' (where X is the ID of the consultation).")."\n"; 
		}
		
		$consultation = new Consultation();
		if ($consultation->load($params['consultation'])) {
			$preparation = new Preparation();
			return $preparation->send_convocations_for_consultation($consultation);
		} else {
			return false;
		}
	}

	function update() {
		$update = new Update();
		$current = $update->current() + 1;
		$last = $update->last();

		for ($i = $current; $i <= $last; $i++) {
			if (method_exists($update, "to_".$i)) {
				$update->{"to_".$i}();
				$update->config("version", $i);
			}
		}
	}

	function execute($argv, $get) {
		if ($argv and count($argv) > 1) {
			$params = array();

			if (substr($argv[1], 0, 2) == "--") {
				$method = substr($argv[1], 2);

				foreach (array_slice($argv, 2) as $i => $arg) {
					if ($arg == "--") {
						$params += array_slice($argv, 3 + $i);
						break;
					}

					if (strpos($arg, "=")) {
						list($key, $value) = explode("=", $arg);
						$params[$key] = $value;
					} else {
						$params[] = $arg;
					}
				}
			}
		} else if ($get) {
			$method = key($get);
			$params = array_slice($get, 1);
		}

		if (isset($method) and method_exists($this, $method)) {
			return $this->$method($params);
		} else {
			return $this->help();
		}
	}
	
	function help() {
		$this_class_name = get_class($this);
		$help = __("Methods available with %s:", array($this_class_name))."\n";
		$class = new ReflectionClass($this_class_name);
		foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
			if ($method->class == $this_class_name and strpos($method->name, "_") !== 0 and $method->name != "execute") {
 				$help .= "--".$method->name."\n";
			}
		}

		return $help;
	}
}
