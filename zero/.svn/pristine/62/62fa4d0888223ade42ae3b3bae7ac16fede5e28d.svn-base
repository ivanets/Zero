<?php
/**
* Class for "Zero Framework"
* @author Nickeras
* @version 0.5
* @package Zero
*/
namespace Zero\Config;
/**
 * Wrap configuration Php file.
 * Extends Config
 */
class Php extends Config {

	function readFile ($fileName) {
		$this->_config = include $fileName;
	}

	function readObject ($array) {
		$this->_config = $array;
	}
}