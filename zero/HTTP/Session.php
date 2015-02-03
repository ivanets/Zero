<?php
/**
* Class for "Zero Framework"
* @author Onegin
* @version 0.2
* @package Zero
*/
namespace Zero\HTTP;
/**
 * Wrap request.
 * Implements ArrayAccess
 */
class Session implements \ArrayAccess {

	function __construct()	{
		if (session_status() == PHP_SESSION_NONE) {
		    session_start();
		}
	}

	//ArrayAccess

	function offsetExists($offset) {
		return array_key_exists($offset, $_SESSION);
	}

	function offsetGet($offset) {
		if (array_key_exists($offset, $_SESSION))
			return $_SESSION[$offset];
		else
			return null;
	}

	function offsetSet($offset, $value) {
		$_SESSION[$offset] = $value;
	}

	function offsetUnset($offset) {
		unset($_SESSION[$offset]);
	}

}