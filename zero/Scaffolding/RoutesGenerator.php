<?php

namespace Zero\Scaffolding;

class RoutesGenerator {

	private $plan = [];
	private $wildcard = '[a-zA-Z0-9_]+';

	function __construct (ApplicationArchitecture $arch) {
		$this->plan = $arch->getRoutesPlan();
	}

	public function makeRoutes($routesFile, $force = false) {
		$routes = [];
		foreach($this->plan as $route) {
			$params = $route['params'];
			$routes[] = [
				'method' => $route['method'],
				'pattern' => '#^'.preg_replace_callback('#(\/?)\{([^\}]*)\}#', function($reg) use ($params) {return $this->parseRouteParam($reg, $params);}, $route['pattern']).'$#uD',
				'controller' => $route['controller'],
				'action' => $route['action']
			];
		}
		if (!file_exists($routesFile) || $force) {
			file_put_contents($routesFile, json_encode($routes, JSON_PRETTY_PRINT));
			chmod($routesFile, 0775);
		}
	}

	private function parseRouteParam($reg_param, $param_list) {
		$prefix = $reg_param[1];
		$reg_array = explode(':', $reg_param[2]);
		$reg = isset($reg_array[1])?$reg_array[1]:$this->wildcard;
		$name_array = explode('=', $reg_array[0]);
		$name = $name_array[0];
		$optional = isset($param_list[$name]);
		$prefix .= ($prefix && $optional)? '?' : '';
		$this->args[$name] = isset($name_array[1])?$name_array[1]:null;
		return $prefix.'(?P<'.$name.'>'.$reg.')'.($optional? '?' : '');
	}
}