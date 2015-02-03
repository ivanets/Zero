<?php
/**
* Class for "Zero Framework"
* @author Nickeras
* @version 0.3
* @package Zero
*/

namespace Zero\View;
/**
 * Basic class for all views
 */
abstract class View {
	/**
	 * @var mixed data for rendering
	 */
	protected $data = false;
	/**
	 * Sets data.
	 * @param mixed $data
	 */
	public function setData($data) {
		$this->data = $data;
	}

	public function sendHeaders() {
		return $this;
	}
	/**
	 * Renders data.
	 */
	abstract public function render();
}