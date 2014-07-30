<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

abstract class Record {
	public $id = 0;

	protected $db = null;

	function __construct(db $db = null) {
		if ($db === null) {
			$db = new db();
		}

		$this->db = $db;
	}

	function db($db) {
		if ($db instanceof db) {
			$this->db = $db;
		}
	}
	
	function fill($hash) {
		if (is_array($hash)) {
			foreach ($hash as $key => $value) {
				$this->{$this->hash_key_to_object_property($key)} = $value;
			}
		}

		return $this;
	}

	function reset() {
		foreach ($this->get_public_properties() as $property => $default_value) {
			$this->{$property} = $default_value;
		}

		return $this;
	}
	
	function as_array() {
		$reflect = new ReflectionClass($this);
		$properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
		
		$elements = array();
		foreach ($properties as $property) {
			$elements[$property->getName()] = $this->{$property->getName()};
		}
		return $elements;
	}

	function as_json() {
		return json_encode($this->as_array());
	}
	
	protected function match_existing($patterns, $table, $db = null) {
		if (sizeof($patterns) > 0) {
			if ($db === null) {
				$db = $this->db;
			}
			$where = array();
			foreach ($patterns as $field => $pattern) {
				if (is_numeric($field)) {
					$where[] = $this->object_property_to_db_column($pattern)." = ".$db->quote($this->$pattern);
				} else {
					$where[] = $this->object_property_to_db_column($field)." = ".$db->quote($pattern);
				}
			}
			$this->id = $db->get_value("
				SELECT id
				FROM ".$table."
				WHERE " . join(" AND ", $where)."
				ORDER BY id DESC
				LIMIT 0, 1
			");
			return $this->id !== null;
			
		} else {
			return false;
		}

	}

	protected function load($id, $table, $columns = null) {
		if ($id === null and ($this->id === null or $this->id == 0)) {
			return false;
		} else {
			if ($id === null) {
				$id = $this->id;
			}
		}

		if ($columns === null) {
			$columns = $this->get_db_columns();
		}

		$result = $this->db->query("
			SELECT ".join(", ", $columns)."
			FROM ".$table."
			WHERE id = ".(int)$id
		);
		
		$row = $this->db->fetchArray($result[0]);

		if ($row === false) {
			return false;
		} else {
			foreach ($row as $column => $value) {
				$this->{$this->db_column_to_php_property($column)} = $value;
			}
				
			return true;
		}
	}

	protected function object_property_to_db_column($property) {
		switch ($property) {
			default:
				return $property;
		}
	}

	protected function hash_key_to_object_property($property) {
		switch ($property) {
			default:
				return $property;
		}
	}

	protected function get_public_properties() {
		$class_name = get_class($this);

		$properties = array();
		if (isset($properties[$class_name]) === false) {
			$default_values = get_class_vars($class_name);
			$class = new reflectionClass($class_name);
			foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
				$property_name = $property->getName();
				$properties[$class_name][$property_name] = $default_values[$property_name];
			}
		}

		return $properties[$class_name];
	}

	protected function db_column_to_php_property($column) {
		$class_name = get_class($this);

		$properties = array();
		if (isset($properties[$class_name]) === false) {
			foreach (array_keys($this->get_public_properties()) as $property) {
				$properties[$class_name][$this->object_property_to_db_column($property)] = $property;
			}
		}

		return $properties[$class_name][$column];
	}

	protected function get_db_columns() {
		$class_name = get_class($this);

		$columns = array();
		if (isset($columns[$class_name]) === false) {
			foreach (array_keys($this->get_public_properties()) as $property) {
				$columns[$class_name][] = $this->object_property_to_db_column($property);
			}
		}

		return $columns[$class_name];
	}
}
