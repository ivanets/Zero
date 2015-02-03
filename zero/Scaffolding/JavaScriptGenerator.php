<?php

namespace Zero\Scaffolding;

class JavaScriptGenerator {

	private $plan = [];
	private $controllers = [];
	
	function __construct (ApplicationArchitecture $arch) {
		$this->plan = $arch->getPlan();

		$controllers = [];
		foreach($this->plan as $v) {
			$parts = explode('\\', $v['controller']);
			$controllers[$v['controller']]['name'] = array_pop($parts);
			$controllers[$v['controller']]['namespace'] = implode('\\', $parts);
		
			if (!isset($controllers[$v['controller']]['actions'][$v['action']]))
				$controllers[$v['controller']]['actions'][$v['action']] = [];
			$controllers[$v['controller']]['actions'][$v['action']] = array_merge($controllers[$v['controller']]['actions'][$v['action']], $v['params']);
			asort($controllers[$v['controller']]['actions'][$v['action']]);
				
			if (!isset($controllers[$v['controller']]['models']))
				$controllers[$v['controller']]['models'] = [];
				
			$models = [];
			foreach ($v['models'] as $m) {
				$models[strpos($m, '\\')?lcfirst(str_replace('\\', '', $m)):$m] = $m;				
			}
			$controllers[$v['controller']]['models'] = array_unique(array_merge($controllers[$v['controller']]['models'], $models));
			
			if (!isset($controllers[$v['controller']]['services']))
				$controllers[$v['controller']]['services'] = [];
			$controllers[$v['controller']]['services'] = array_values(array_unique(array_merge($controllers[$v['controller']]['services'], $v['services'])));				
			
		}
		$this->controllers = $controllers;
	}

	private function parseTemplate($_DATA) {
/*		ob_start();
		include(__DIR__.'/templates/controller.tpl.php');
		return ob_get_clean();			
*/
	}
	
	public function makeJavaScript($jsRestFile, $force = false) {
/*
		foreach($this->controllers as $controller) {
			$controller['app'] = $appNamespace;
			$body = "<?php\n".$this->parseTemplate($controller);
			$dir = trim($controllersFolder.'/'.str_replace('\\', '/', $controller['namespace']), '/');
			$file = $dir.'/'.$controller['name'].'.php';
			if (!file_exists($dir)) {
				mkdir($dir, 0775, true);
				chmod($dir, 0775);
			}
			if (!file_exists($file) || $force) {
				file_put_contents($file, $body);
				chmod($file, 0775);
			}
		}
*/		
	}
}