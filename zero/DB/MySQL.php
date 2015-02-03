<?php
/**
* Class for "Zero Framework"
*/

namespace Zero\DB;

/**
* MySQL
*
* MySQL extends mysqli db connector.
* Class for "Zero Framework" implements
* <ul>
* <li>aliasing config rule names</li>
* <li>Comunicate with DB</li>
* <li>Realize helping functions for preparing data</li>
* </ul>
* @author Nickeras, Onegin
* @version 0.5
* @package Zero
*/
class MySQL extends \mysqli{
/**
* Alias Array
*
* @staticvar array List of aliases for aliasing DB credentials key names
*/
	private $debug = false;

/**
* escape
*
* Wrapper of real_escape_string. Can make escape string or escape array
* @param string|array $toEscape Text or text array to SQL escape
* @return string|array Escaped text
*/
	function escape($toEscape) {
		if (is_array($toEscape)) {
			foreach ($toEscape as $key => $value) {
				$toEscape[$key] = $this->real_escape_string($value);
			}
			return $toEscape;
		} else
			return $this->real_escape_string($toEscape);
	}
/**
* queryToArray
*
* Execute query string and returns array of selected values
* @param string $query SQL query string
* @return array Result of query treated as array
*/
	function queryToArray ($query) {
		$this->escape($query);
		if ($this->debug)
			echo $query;
		$array = array();
		if ($result = parent::query($query)) {
			while ($row = $result->fetch_assoc()) {
				$array[] = $row;
			}
		}
		return $array;
	}

	//wrapper for query method
	function query ($query) {
		if ($this->debug)
			echo $query;
		parent::query($query);
	}

	//Set debug flag to show all executing queries
	function setDebug ($flag = true) {
		$this->debug = (bool)$flag;
	}
}