<?php
/**
* Class for "Zero Framework"
* @author Onegin, Nickeras
* @version 0.5
* @package Zero
*/

namespace Zero\Scaffolding;
/**
 * Generating basic code by plan.
 */
class ApplicationArchitecture {

	private $plan;

	function __construct ($planFile) {
		if (!$planFile && !file_exists($planFile)) {
			throw new \Exception('Can\'t find plan file: '.$planFile, 601);
		}
		$this->plan = $this->parse($this->load($planFile));
	}
/**
 * Load plan file.
 * @param string $file filename
 * @return array plan
 */
	private function load($file) {
		$plan = file($file);

		foreach ($plan as $k => $v) {
			$comment = strpos($v, '#');
			$plan[$k] = trim( ($comment !== false ) ? substr($v, 0, $comment) : $v );
			if (!$plan[$k])
				unset($plan[$k]);
		}
		$plan = array_values($plan);
		$count = count($plan);
		for($k = 0; $k < $count; $k++) {
			$v = $plan[$k];
			if (strpos($v, '@') === 0 ) {
				$include = dirname($file)."/".substr(trim($v),1);
				if (file_exists($include)) {
					array_splice($plan, $k, 1, $this->load($include));
				} else {
					unset($plan[$k]);
				}
			}
		}
		return $plan;
	}
/**
 * Parse plan file.
 * Transform rules to regexp and aggregate data
 * @param array $plan plan
 * @return array parsed plan
 */
	private function parse($plan) {
		$parsed = [];
		$parents = [];

		foreach($plan as $k => $row) {
			if ($row == '{') {
				continue;
			}
			if ($row == '}') {
				array_pop($parents);
				continue;
			}
			$parts = explode(' ', $row);
			if(isset($parts[0]) && strpos($parts[0], '*')===0){
				//Controllers
				$controller = [];
				$controller['name'] = (substr($parts[0], 1));

				for ($i=1; $i < count($parts); $i++) {
					$p = trim(trim($parts[$i], '<'),'>');
					$p = explode('$', $p);
					$controller['params'][] = $p;
				}
				$parsed['controllers'][$controller['name']] = $controller;

			} elseif(isset($parts[0]) && strpos($parts[0], '!')===0){
				//Models
				$model = [];
				$model['name'] = (substr($parts[0], 1));

				for ($i=1; $i < count($parts); $i++) {
					$p = trim(trim($parts[$i], '<'),'>');
					$p = explode('$', $p);
					$model['params'][] = $p;
				}
				$parsed['models'][$model['name']] = $model;

			} else {
				$method = array_shift($parts);
				$pattern = array_shift($parts);
				if($method=='ROUTE'){
					if (isset($plan[$k+1]) && $plan[$k+1] == '{') {
						$rule = [
							'method' => $method,
							'params' => [],
							'pattern' => $pattern
						];
						if (!empty($parents)) {
							$p = $parents[count($parents)-1];
							if($rule['pattern']!='/')
								$rule['pattern'] = $p['pattern'].$rule['pattern'];
							else
								$rule['pattern'] = $p['pattern'];
						}
						array_push($parents, $rule);
					}
					continue;
				}
				$controller = array_shift($parts);

				$controller = explode('.', $controller);
				if (!isset($controller[1]))
					$controller[1] = 'index';

				$params = [];
				preg_match_all('#\{([^:\}]*)[:\}]#', $pattern, $var);
				$params = array_fill_keys($var[1], null);


				$models = [];
				$services = [];
				$js = false;
				foreach ($parts as $v) {
					$v = trim($v);
					if (preg_match('#^\[(.*)=(.*)\]$#', $v, $default)) {
						$params[$default[1]] = $default[2];
					} elseif (preg_match('#^<(.*)>$#', $v, $model)) {
						$models[] = $model[1];
					} elseif (preg_match('#^\((.*)\)$#', $v, $service)) {
						$services[] = $service[1];
					} elseif ($v == '$') {
						$js = true;
					}
				}

				$rule = [
					'method' => $method,
					'pattern' => $pattern,
					'controller' => $controller[0],
					'action' => $controller[1],
					'params' => $params,
					'models' => $models,
					'services' => $services,
					'js' => $js
				];

				if (!empty($parents)) {
					$p = $parents[count($parents)-1];
					if($rule['pattern']!='/')
						$rule['pattern'] = $p['pattern'].$rule['pattern'];
					else
						$rule['pattern'] = $p['pattern'];

					if (!$rule['controller'])
						$rule['controller'] = $p['controller'];
				}

				if (isset($plan[$k+1]) && $plan[$k+1] == '{') {
					array_push($parents, $rule);
				}

				if (!$rule['controller']) {
					throw new \Exception("There's no controller in plan", 600);
					continue;
				}

				$parsed['routes'][] = $rule;
			}
		}
		// var_dump($parsed['controllers']);die;
		return $parsed;
	}
/**
 * Get Routes plan.
 * @return array plan
 */
	public function getRoutesPlan() {
		return isset($this->plan['routes'])?$this->plan['routes']:[];
	}
/**
 * Get Controllers plan.
 * @return array plan
 */
	public function getControllersPlan() {
		return isset($this->plan['controllers'])?$this->plan['controllers']:[];
	}
/**
 * Get Models plan.
 * @return array plan
 */
	public function getModelsPlan() {
		return isset($this->plan['models'])?$this->plan['models']:[];
	}
}