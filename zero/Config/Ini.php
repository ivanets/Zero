<?php
/**
* Class for "Zero Framework"
* @author Nickeras
* @version 0.3
* @package Zero
*/
namespace Zero\Config;
/**
 * Wrap configuration INI file.
 * Extends Config
 */
class Ini extends Config {

	function readFile ($fileName) {
		$this->_config = parse_ini_file($fileName, true);
	}

	function readObject ($string) {
		$this->_config = parse_ini_string($string, true);
	}

}