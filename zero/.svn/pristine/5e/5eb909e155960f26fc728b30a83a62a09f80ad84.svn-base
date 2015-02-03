<?php
/**
* Class for "Zero Framework"
* @author Nickeras
* @version 0.3
* @package Zero
*/
namespace Zero\Config;
/**
 * Wrap configuration file.
 * Implements ArrayAccess
 */
abstract class Config implements \ArrayAccess {
/**
 * @var array parsed config
 */
	protected $_config = [];
/**
 * @param string|object $config Filename or Object
 */
	function __construct ($config) {
		if (file_exists($config)) {
			$this->readFile($config);
		} else {
			$this->readObject($config);
		}
	}
/**
 * Parses config data from file.
 * @param string $fileName filename
 */
	abstract function readFile ($fileName);
/**
 * Parses config data from object.
 * @param object $object Object
 */
	abstract function readObject ($object);


	//ArrayAccess

	function offsetExists($offset) {
		return array_key_exists($offset, $this->_config);
	}

	function offsetGet($offset) {
		return (array_key_exists($offset, $this->_config))?$this->_config[$offset]:false;
	}

	function offsetSet($offset, $value) {}

	function offsetUnset($offset) {}

}