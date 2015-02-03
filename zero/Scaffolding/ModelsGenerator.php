<?php

namespace Zero\Scaffolding;

class ModelsGenerator {

	private $plan = [];
	private $models = [];

	function __construct (ApplicationArchitecture $arch) {
		$this->plan = $arch->getModelsPlan();
		$this->routesPlan = $arch->getRoutesPlan();

		$modelsList = [];
		foreach($this->routesPlan as $v) {
			$modelsList = array_values(array_unique(array_merge($modelsList, $v['models'])));
		}
		foreach($this->plan as $k => $v) {
			$modelsList[] = $k;
		}

		$models = [];
		foreach($modelsList as $v) {
			$parts = explode('\\', $v);
			$name = array_pop($parts);
			$namespace = implode('\\', $parts);
			$params = [];
			if (isset($this->plan[$v]['params'])){
				foreach ( $this->plan[$v]['params'] as $m) {
					$prefix = false;
					if(strpos(strtolower($m[0]), 'zero')!==false){
						$prefix = true;
					}

					if(isset($m[1])){
						$params[$m[1]] = $prefix?$m[1]:$m[0];
					} else {
						$paramName = strpos($m[0], '\\')?lcfirst(str_replace('\\', '', $m[0])):$m[0];
						$params[$paramName] = $prefix?$paramName:$m[0];
					}
				}
			}

			$models[$v] = [
				'name' => $name,
				'namespace' => $namespace,
				'params' => $params
			];
		}
		$this->models = $models;
	}

	private function parseTemplate($_DATA) {
		ob_start();
		include(__DIR__.'/templates/model.tpl.php');
		return ob_get_clean();
	}

	public function makeModels($modelsFolder, $appNamespace, $force = false) {
		foreach($this->models as $model) {
			$model['app'] = $appNamespace;
			$body = "<?php\n".$this->parseTemplate($model);
			$dir = trim($modelsFolder.'/'.str_replace('\\', '/', $model['namespace']), '/');
			$file = $dir.'/'.$model['name'].'.php';
			if (!file_exists($dir)) {
				mkdir($dir, 0775, true);
				chmod($dir, 0775);
			}
			if (!file_exists($file) || $force) {
				file_put_contents($file, $body);
				chmod($file, 0775);
			}
		}
	}
}