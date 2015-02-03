<?php
/**
* Class for "Zero Framework"
*/
namespace Zero\Reflection;
/**
 * Exposing value of private variable in object.
 * @author Nickeras
 * @version 0.2
 * @package Zero
 */
class Exposer {

	private $obj;
	private $class;
/**
 * @param object $obj object
 */
	function __construct($obj) {
		$this->obj = $obj;
		$this->class = new \ReflectionClass(get_class($obj));

	}
/**
 * @param string $propertyName property name
 * @return mixed property value
 */
	function getValue($propertyName) {
		$property = $this->class->getProperty($propertyName);

		if ( $property->isPrivate() || $property->isProtected() )
			$property->setAccessible(true);

		$val = $property->getValue($this->obj);

		if ( $property->isPrivate() || $property->isProtected() )
			$property->setAccessible(false);

		return $val;
	}
}