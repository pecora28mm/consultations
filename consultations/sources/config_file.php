<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Config_File {
	public $content = null;
	public $readonly_entries = array();
	protected $path = "";
	protected $type = "config";

	function __construct($path, $type="config") {
		$this->path = $path;
		$this->type = $type;
	}

	function __toString() {
		return $this->path;
	}
	
	function type() {
		return $this->type;
	}

	function get_path() {
		return $this->path;
	}

	function get_directory() {
		return dirname($this->path);
	}

	function free() {
		$this->content = null;
		return $this;
	}

	function read() {
		$this->free();

		$content = file_get_contents($this->path);

		if ($content === false) {
			throw new Exception("Unable to read file ".$this->path);
		} else {
			$this->content = $content;
			return $this;
		}
	}

	function exists() {
		return file_exists($this->path);
	}

	function is_writable() {
		return is_writable($this->path);
	}

	function is_readable() {
		return is_readable($this->path);
	}

	function is_readonly($name) {
		return array_search($name, $this->readonly_entries) !== false;
	}

	function write() {
		if ($this->content === null) {
			trigger_error("Content of file ".$this->path." must not be null", E_USER_ERROR);
		} else {
			$size = @file_put_contents($this->path, $this->content);

			if ($size === false || $size < strlen($this->content)) {
				 throw new Exception("Unable to write in ".$this->path);
			} else {
				return $this;
			}
		}
	}

	function write_value($name, $value) {
		if ($this->is_writable() and !$this->is_readonly($name)) {
			foreach (file($this->path) as $line) {
				if (preg_match('|^(\\$[^[]+\\[\''.$name.'\'\\]\s*=\s*")[^"]*(";.*)$|u', $line, $parameters)) {
					$contents[] = $parameters[1].$value.$parameters[2]."\n";
				} else {
					$contents[] = $line;
				}
			}
			$this->content = join("", $contents);

			return $this->write();
		}

		return false;
	}

	function copy(config_file $config_file) {
		$this->content = $config_file->read()->content;
		return $this->write();
	}

	function add($key, $value, $type = null, $comment = "") {
		if ($type === null) {
			$type = $this->type();
		}
		if (!empty($comment)) {
			$comment = "\t\t// ".$comment;
		}
		$this->read();
		$this->content .= "\n## update ".date("d/m/Y H:i", time())."\n";
		$this->content .= "\$".$type."['".$key."'] = \"".$value."\";".$comment."\n";

		return $this->write();

	}

	function remove($key) {
		if (!$this->is_writable()) {
			return false;
		} else {
			$contents = array();

			foreach (file($this->path) as $line) {
				if (!preg_match('|^\\$([^[]+)\\[\'([^\']+)\'\\]\s*=\s*"([^"]*)"(;.*)$|u', $line, $parameters)) {
					$contents[] = $line;
				} else if ($parameters[2] != $key) {
					$contents[] = $line;
				} else if ($this->is_readonly($parameters[2])) {
					$contents[] = $line;
				}
			}

			return file_put_contents($this->path, join("", $contents)) !== false;
		}
	}

	function update($values) {
		if (!$this->is_writable()) {
			return false;
		} else {
			if (!isset($values[$this->type()])) {
				return false;
			} else {
				$values = $values[$this->type()];
				$contents = array();

				foreach (file($this->path) as $line) {
					if (!preg_match('|^\\$([^[]+)\\[\'([^\']+)\'\\]\s*=\s*"([^"]*)"(;.*)$|u', $line, $parameters) || !isset($values[$parameters[1]][$parameters[2]])) {
						$contents[] = $line;
					} else if ($this->is_readonly($parameters[2])) {
						$contents[] = $line;
					} else {
						$contents[] = '$'.$parameters[1].'[\''.$parameters[2].'\'] = "'.$values[$parameters[1]][$parameters[2]].'"'.$parameters[4]."\n";
					}
				}

				return file_put_contents($this->path, join("", $contents)) !== false;
			}
		}
	}

	function values() {
		if (!$this->is_readable()) {
			return false;
		} else {
			$values = array();
			foreach (file($this->path) as $line) {
				if (preg_match('|^\\$([^[]+)\\[\'(.*)\'\\]\s*=\s*"([^"]*)";.*$|u', $line, $parameters)) {
					$parameters[2] = stripslashes($parameters[2]);
					$values[$parameters[1]][$parameters[2]] = $parameters[3];
				}
			}

			return $values;
		}
	}

	function read_value($value) {
		if ($this->is_readable()) {
			$values = array();
			foreach (file($this->path) as $line) {
				if (preg_match('|^\\$'.$this->type.'\\[\''.$value.'\'\\]\s*=\s*"([^"]*)";.*$|u', $line, $parameters)) {
					return $parameters[1];
				}
			}
		}
		return false;
	}

	function load_at_global_level() {
		if (!$this->is_readable()) {
			return false;
		} else {
			foreach (file($this->path) as $line) {
				if (preg_match('|^\\$([^[]+)\\[\'(.*)\'\\]\s*=\s*"([^"]*)";.*$|u', $line, $parameters)) {
					$parameters[2] = stripslashes($parameters[2]);
					$GLOBALS[$parameters[1]][$parameters[2]] = $parameters[3];
				}
			}
			return true;
		}
	}
}
