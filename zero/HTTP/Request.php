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
class Request implements \ArrayAccess {

	private $_values = [];
	private $headers = [];
	private $method = false;
	private $protocol = false;

	function __construct()	{
		if (PHP_SAPI == "cli") return NULL;
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->protocol = $_SERVER['SERVER_PROTOCOL'];
		$this->headers = apache_request_headers();
		$val = [];
		switch ($this->method) {
			case 'OPTION':
			case 'HEAD':
			case 'DELETE':
			case 'PUT':
				$val = file_get_contents('php://input');
				if($this->isJson($val))
					$val = json_decode($val, true);
				break;
			case 'POST':
				$val = $_POST;
				if(!$val){
					$val = file_get_contents('php://input');
					if($this->isJson($val))
						$val = json_decode($val, true);
				}
				break;
			case 'GET':
				$val = $_GET;
				break;
			default:
				$val = [];
				break;
		}

		if ($this->method != 'GET' && !empty($_GET)) {
			$val = array_merge($_GET, $val);
		}
		$this->_values = $this->recursiveEscape($val);
	}

	private function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	function getMethod(){
		return $this->method;
	}

	function getProtocol(){
		return $this->protocol;
	}

	function getHeaders(){
		return $this->headers;
	}

	private function escape($element){
		return trim(htmlspecialchars($element,ENT_QUOTES));
	}

	private function recursiveEscape($tmp){
		if (!is_array($tmp)) {
			return $this->escape($tmp);
		} else {
			$new = [];
			foreach ($tmp as $k => $v) {
				$new[$this->escape($k)] = $this->recursiveEscape($v);
			}
			return $new;
		}

	}

	//ArrayAccess

	function offsetExists($offset) {
		return array_key_exists($offset, $this->_values);
	}

	function offsetGet($offset) {
		if (array_key_exists($offset, $this->_values))
			return $this->_values[$offset];
		else
			return null;
	}

	function offsetSet($offset, $value) {}

	function offsetUnset($offset) {}

}