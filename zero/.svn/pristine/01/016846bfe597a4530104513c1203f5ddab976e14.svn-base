<?php
/**
* Class for "Zero Framework"
* @author Nickeras
* @version 0.5
* @package Zero
*/
namespace Zero\Config;
/**
 * Wrap configuration Json file.
 * Extends Config
 */
class Json extends Config {

	function readFile ($fileName) {
		$json = file_get_contents($fileName);
		$this->_config = json_decode($json, true);
	}

	function readObject ($json) {
		$this->_config = json_decode($json, true);
	}
}