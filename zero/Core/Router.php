<?php
/**
* Class for "Zero Framework"
*/
namespace Zero\Core;
/**
 * Realize application routing.
 * Parses route file
 * @author Nickeras
 * @version 0.4
 * @package Zero
 */
class Router {
	private $routes;
	private $url;
	private $http_method;
/**
 * Parses url, http method and load route files
 * @param string $routesFile routes file location
 */
	function __construct ($routesFile) {
		if (!$routesFile || !file_exists($routesFile)) {
			throw new \Exception('Can\'t find route file: '.$routesFile, 511);
		}

		$url = parse_url($_SERVER['REQUEST_URI']);
		$this->url = '/'.trim($url['path'], '/');
		$this->http_method = $_SERVER['REQUEST_METHOD'];
		$this->routes = json_decode(file_get_contents($routesFile), true);
	}
/**
 * Match and Return current route.
 * @return string|bool Route or false if nothing matches
 */
	public function getRoute() {
		foreach($this->routes as $route) {
			if ( ( '*'==$route['method'] || $this->http_method==$route['method'] ) && preg_match($route['pattern'], $this->url, $route['params'])){
				array_shift($route['params']);
				return $route;
			}
		}
		return false;
	}
}