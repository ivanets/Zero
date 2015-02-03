<?php
/**
* Class for "Zero Framework"
*/
namespace Zero\Core;
/**
* Basic class to initialize application.
* Zero application
* @author Onegin
* @version 0.6
* @package Zero
*/
class Zero {
/**
 * @var string $controllersFolder Controllers folder location
 */
	private $controllersFolder = 'controllers';
/**
 * @var string $modelsFolder Models folder location
 */
	private	$modelsFolder = 'models';
/**
 * @var string $webFolder Public web folder location
 */
	private	$webFolder = '../web';
/**
 * @var string|bool $routesFile File with route rules
 */
	private	$routesFile = false;
/**
 * @var string $appNamespace Basic Application Namespace
 */
	private	$appNamespace = '\\';
/**
 * @var mixed $services Services array
 */
	private	$services = [];
/**
 * @var mixed $models Models array
 */
	private	$models = [];
/**
 * @var mixed $router Router instance
 */
	private	$router;
/**
 * @var mixed $view View instance
 */
	private	$view = false;
/**
 * Constructor
 * @param array|bool $env parsed config file
 */
	function __construct ($env = false) {
		if ($env)
			$this->setEnv($env);
		$this->userAutoloader();

	}


	public function userAutoloader(){
		spl_autoload_register(function ($className) {
			$className = str_replace($this->appNamespace, '', $className);
			$folder = '';
			if(strpos(strtolower($className), 'model')!==false){
				$folder = $this->modelsFolder;
			}elseif(strpos(strtolower($className), 'controller')!==false){
				$folder = $this->controllersFolder;
			}
			$path = getcwd().'/'.str_replace('\\', '/', $folder.'\\'.$className).'.php';
			echo $path;
			if (file_exists($path)) {
				require_once $path;
			}
		});
	}
/**
 * Provides creating controller instance and calls method by name.
 * Checks for parameters in method and theirs optionality
 * @param string $controllerName
 * @param string $controllerName
 * @param mixed $controllerName
 * @return object Controller instance
 */
	private function dispatch($controllerName, $method, $params) {
		$method .= 'Action';
		$controllerFile = $this->controllersFolder.'/'.str_replace('\\', '/', $controllerName).'.php';
		if (file_exists($controllerFile)) {
			include_once ($controllerFile);
			$controllerName = $this->appNamespace.$controllerName;

			try{
				$controller = new $controllerName($this);
			}catch(\Exception $ex){
				throw new \Exception("Error create instance of ".$controllerName." because of ".$ex->getMessage(), 111);
			}
			if (method_exists($controller, $method) && is_callable(array($controller, $method))) {
				$action = new \ReflectionMethod($controller, $method);
				$parameters = $action->getParameters();
				$values = [];
				foreach ($parameters as $p) {
					$name = $p->getName();
					if (isset($params[$name]) && $params[$name]) {
						$values[$p->getPosition()] = $params[$name];
					} elseif ($p->isOptional()) {
						$values[$p->getPosition()] = $p->getDefaultValue();
					} else {
						throw new \Exception("Error while comparing parameters for ".$controllerName."->".$method, 112);
					}
				}
				$data = call_user_func_array(array($controller, $method), $values);
			} else {
				throw new \Exception("No such method ".$method." in controller ".$controllerName, 113);
				$data = false;
			}
		} else {
			throw new \Exception("There's no controller file ".$controllerFile, 114);
			$data = false;
		}
		return $data;
	}
/**
 * Loading model by name.
 * @param string $modelName
 * @return object Model instance
 */
	private function loadModel($modelName) {
		$modelFile = $this->modelsFolder.'/'.str_replace('\\', '/', $modelName).'.php';
		if (file_exists($modelFile)) {
			include_once ($modelFile);
			$modelName = $this->appNamespace.$modelName;
			try{
				$model = new $modelName($this);
			}catch(\Exception $ex){
				throw new \Exception("Error create instance of ".$modelName." because of ".$ex->getMessage(), 121);
			}
			return $model;
		} else {
			throw new \Exception("There's no model file ".$modelFile, 124);
			return false;
		}
	}
/**
 * Run application.
 * Checks for route rule and run application to combine data and render to view
 * @param bool $render checks if render is needed
 * @return Zero App
 */
	public function run($render = false) {
		try {
			if (PHP_SAPI == "cli") {
				global $argv;

				$forceRoutes = false;
				if ( ($k=array_search('--routes', $argv)) !== false ) {
					$forceRoutes = true;
					unset($argv[$k]);
					$argv = array_values($argv);
				}
				$force = false;
				if ( ($k=array_search('--force', $argv)) !== false ) {
					$force = true;
					unset($argv[$k]);
					$argv = array_values($argv);
				}

				$command = isset($argv[1])?strtolower($argv[1]):'';
				$arg = isset($argv[2])?$argv[2]:'';

				switch ($command) {
					case 'run':
						$cli_route = explode('.', $arg);
						$params = [];
						for($i=3;$i<count($argv);$i++) {
							$p = explode('=', $argv[$i]);
							if (count($p) == 2) {
								$params[$p[0]] = $p[1];
							}
						}
						$route = [
							"controller" => str_replace('/', '\\', $cli_route[0]),
							"action" => (isset($cli_route[1])?$cli_route[1]:''),
							"params" => $params
						];
						break;
					case 'scaffolding':
						$cli = isset($_ENV['cli'])?$_ENV['cli'].'/':'';
						return $this->scaffolding($cli.$arg, $force, $forceRoutes);
					default:
						echo "unknown command\n";
						return false;
				}

			} else {

				if ($this->routesFile) {
					$this->router = new Router($this->routesFile);
				} else {
					throw new \Exception("There's no route file ", 311);
				}

				if (!$this->router) {
					throw new \Exception("Error creating router", 312);
				}

				$route = $this->router->getRoute();

			}
			if ($route) {
				$data = $this->dispatch($route['controller'], $route['action'], $route['params']);
			} else {
				// dirty hack for remote debug
				if(isset($_COOKIE['XDEBUG_SESSION']) && $_COOKIE['XDEBUG_SESSION']){
					return $this;
				}
				throw new \Exception("No route", 313);
			}

			if ($this->view && $this->view instanceof \Zero\View\View) {
				$this->view->setData($data);
				if ($render) {
					$this->view->sendHeaders();
					return $this->view->render();
				}
			} else {
				throw new \Exception("Error while creating view", 314);
			}
		} catch (\Exception $e) {
			if (PHP_SAPI == "cli") {
				echo $e->getMessage()."\n";
				die;
			} else {
				throw $e;
			}
		}
		return $this;
	}
/**
 * Scaffolding.
 * Generate routes, models and controllers basing on plan file
 * @param string $planFile
 * @param bool $force rewrite existing scaffolding result
 * @return Zero App
 */
	private function scaffolding($planFile, $force = false, $forceRoutes = false) {
		$appArch = new \Zero\Scaffolding\ApplicationArchitecture($planFile, $this);

		if($force && !$forceRoutes){
			$forceRoutes = true;
		}

		if ($this->routesFile) {
			$routesGenerator = new \Zero\Scaffolding\RoutesGenerator($appArch);
			$routesGenerator->makeRoutes($this->routesFile, $forceRoutes);
		} else {
			// var_dump($this->routesFile);
			throw new \Exception("No declarated route file", 411);
		}


		if ($this->controllersFolder) {
			$controllersGenerator = new \Zero\Scaffolding\ControllersGenerator($appArch);
			$controllersGenerator->makeControllers($this->controllersFolder, $this->appNamespace, $force);
		} else {
			throw new \Exception("No declarated controllers folder", 413);
		}


		if ($this->modelsFolder) {
			$modelsGenerator = new \Zero\Scaffolding\ModelsGenerator($appArch);
			$modelsGenerator->makeModels($this->modelsFolder, $this->appNamespace, $force);
		} else {
			throw new \Exception("No declarated models folder", 412);
		}

	}
/**
 * Magic method.
 * Render data into view
 * @return \Zero\View\View data
 */
	public function __toString() {
		if ($this->view && $this->view instanceof \Zero\View\View) {
			$this->view->sendHeaders();
			return $this->view->render();
		} else {
			throw new \Exception("Error while creating view", 314);
		}
		return '';
	}

/**
 * Sets environment variables from config.
 * @param array $config parsed config file
 */
	public function setEnv($config) {
		if (isset($config['appNamespace']) && $config['appNamespace'])
			$this->setAppNamespace($config['appNamespace']);
		if (isset($config['controllersFolder']) && $config['controllersFolder'])
			$this->setControllersFolder($config['controllersFolder']);
		if (isset($config['modelsFolder']) && $config['modelsFolder'])
			$this->setModelsFolder($config['modelsFolder']);
		if (isset($config['routesFile']) && $config['routesFile'])
			$this->setRoutesFile($config['routesFile']);
		if (isset($config['webFolder']) && $config['webFolder'])
			$this->setWebFolder($config['webFolder']);

		if (isset($config['view']) && $config['view']) {
			$viewConfig = $config['view'];
			$params = (isset($viewConfig['params']) && $viewConfig['params'])?$viewConfig['params']:false;
			$type = (isset($viewConfig['type']) && $viewConfig['type'])?$viewConfig['type']:false;
			if ($type) {
				$view = new $type($params);
				if ($view && $view instanceof \Zero\View\View)
					$this->setView($view);
			}
		}
		return $this;
	}
/**
 * Setting application namespace if needed.
 * @param string $namespace
 * @return Zero App
 */
	public function setAppNamespace($namespace = '') {
		$this->appNamespace = trim($namespace, '\\').'\\';
		return $this;
	}

	public function getAppNamespace() {
		return $this->appNamespace;
	}
/**
 * Setting application routes if needed.
 * @param string $routesFile
 * @return Zero App
 */
	public function setRoutesFile($routesFile = false) {
		$this->routesFile = $routesFile;
		return $this;
	}
/**
 * Setting controllers folder.
 * @param string $controllersFolder
 * @return Zero App
 */
	public function setControllersFolder($controllersFolder) {
		$this->controllersFolder = trim($controllersFolder, '/');
		return $this;
	}
/**
 * Setting models folder.
 * @param string $modelsFolder
 * @return Zero App
 */
	public function setModelsFolder($modelsFolder) {
		$this->modelsFolder = trim($modelsFolder, '/');
		return $this;
	}
/**
 * Setting Public web folder.
 * @param string $webFolder
 * @return Zero App
 */
	public function setWebFolder($webFolder) {
		$this->webFolder = trim($webFolder, '/');
		return $this;
	}
/**
 * Setting view.
 * @param Zero\View\View $view
 * @return Zero App
 */
	function setView(\Zero\View\View $view) {
		$this->view = $view;
		return $this;
	}
/**
 * Get view.
 * @return Zero\View\View View
 */
	function getView() {
		return $this->view;
	}
/**
 * Adding service to service list.
 * @param string $key
 * @param object $var
 * @return Zero App
 */
	function setService ($key, $var) {
		$this->services[$key] = $var;
		return $this;
	}
/**
 * Get service by key.
 * @param string $key
 * @return object Service
 */
	function getService ($key) {
		if(strpos(strtolower($key), 'model')!==false){
			return $this->getModel($key);
		}
		if (array_key_exists($key, $this->services)) {
			return $this->services[$key];
		} else {
			throw new \Exception("Service ".$key." doesn't exist", 211);
			return false;
		}
	}
/**
 * Get model by model name.
 * @param string $modelName
 * @return object Model
 */
	public function getModel ($modelName) {
		if (array_key_exists($modelName, $this->models)) {
			return $this->models[$modelName];
		} else {
			if ($model = $this->loadModel($modelName)) {
				return $this->models[$modelName] = $model;
			}
		}
	}
}