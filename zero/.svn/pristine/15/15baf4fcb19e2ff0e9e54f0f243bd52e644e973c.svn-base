<?php
/**
* Class for "Zero Framework"
* @author Nickeras
* @version 0.4
* @package Zero
*/
namespace Zero\View;
/**
 * Represents data in HTML format using Php preprocessing.
 * Extends View
 */
class PhpView extends View {
	/**
	 * @var array $templates container for teplates.
	 */
	private $templates;

	function __construct ($templates) {
		if (is_array($templates))
			$this->templates = $templates;
		else
			$this->templates = [$templates];
	}

	function render() {
		$_DATA = $this->data;

		ob_start();
		foreach ($this->templates as $tpl) {
			$tpl = trim($tpl, '/');
			if(file_exists($tpl)){
				include ($tpl);
			}
		}
		return ob_get_clean();
	}

}