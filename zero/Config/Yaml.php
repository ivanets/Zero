<?php

namespace Zero\Config;

class Yaml extends Config {

	function readFile ($fileName) {
		$yaml = file_get_contents($fileName);
		$this->_config = yaml_parse($yaml);
	}

	function readObject ($yaml) {
		$this->_config = yaml_parse($yaml);
	}
}