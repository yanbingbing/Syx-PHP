<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

require_once 'Syx/Controller.php';

/**
 * Syx_Application
 *
 * @category  Syx
 * @package   Syx_Application
 */
class Syx_Application
{
	/**
	 * current configure
	 *
	 * @var array
	 */
	protected $_config = array();

	/**
	 * controller include paths
	 *
	 * @var string
	 */
	protected $_controllerPaths = '';

	/**
	 * controller suffix
	 *
	 * @var string
	 */
	protected $_controllerSuffix = '';

	/**
	 * Controller key for retrieving controller from request
	 * @var string
	 */
	protected $_controllerKey = 'controller';

	/**
	 * Action key for retrieving action from request
	 * @var string
	 */
	protected $_actionKey = 'action';

	/**
	 * Default action
	 * @var string
	 */
	protected $_defaultAction = 'index';

	/**
	 * Default controller
	 * @var string
	 */
	protected $_defaultController = 'Index';

	/**
	 * Default Request
	 *
	 * @var Syx_Request_Http
	 */
	protected $_request = null;

	/**
	 * Default Response
	 *
	 * @var Syx_Response_Http
	 */
	protected $_response = null;

	/**
	 * The route rules
	 *
	 * @var array
	 */
	protected $_routes = null;

	/**
	 * Options can configure
	 *
	 * @var array
	 */
	protected $_options = array(
		'php', 'controllerPaths', 'includePaths', 'exceptionHandler', 'log',
		'db', 'session', 'controllerSuffix', 'controllerKey', 'routes',
		'actionKey', 'defaultController', 'defaultAction'
	);

	/**
	 * constructor
	 *
	 * @param array|string|Syx_Config $options array or Syx_Config
	 */
	public function __construct($options = null)
	{
		if ($options) {
			require_once 'Syx/Config.php';
			if (is_string($options)) {
				$cfg = new Syx_Config($options);
				$options = $cfg->toArray();
			} elseif ($options instanceof Syx_Config) {
				$options = $options->toArray();
			}
			$this->_config = (array)$options;
			$this->setOptions($this->_config);
		}
	}

	/**
	 * use ini_set to change php runtime settings
	 *
	 * @param array $settings
	 * @param string $prefix
	 * @return Syx_Application
	 */
	public function setPhp(array $settings, $prefix = '')
	{
		foreach ($settings as $key => $value) {
			$key = empty($prefix) ? $key : $prefix . $key;
			if (is_scalar($value)) {
				ini_set($key, $value);
			} elseif (is_array($value)) {
				$this->setPhp($value, $key . '.');
			}
		}

		return $this;
	}

	/**
	 * set include_path for include or require function
	 *
	 * @param string|array $paths 'path;path' or array('path','path/to/dir')
	 * @return Syx_Application
	 */
	public function setIncludePaths($paths)
	{
		if (is_string($paths)) {
			$paths = explode(PATH_SEPARATOR, $paths);
		}
		if (!is_array($paths) || empty($paths)) {
			return $this;
		}
		$paths = array_merge($paths, explode(PATH_SEPARATOR, get_include_path()));
		foreach ($paths as &$path) {
			$path = rtrim(str_replace('\\', '/', $path), '/ ');
		}
		set_include_path(implode(PATH_SEPARATOR, array_unique($paths)));
		return $this;
	}

	/**
	 * set exception_handler
	 *
	 * @param string|array $handler like 'function' or array('object','method')
	 * @return Syx_Application
	 */
	public function setExceptionHandler($handler)
	{
		set_exception_handler($handler);
		return $this;
	}

	/**
	 * set Log writers for logging
	 *
	 * @param array $writers
	 *
	 * @throws Syx_Application_Exception
	 * @return Syx_Application
	 */
	public function setLog(array $writers)
	{
		require_once 'Syx/Log.php';
		$Log = Syx_Log::getInstance();
		foreach ($writers as $writer) {
			if (is_string($writer)) {
				$writer = Syx::loadClass($writer);
				$writer = new $writer();
			} elseif (is_array($writer)) {
				if (!array_key_exists('class', $writer)) {
					require_once 'Syx/Application/Exception.php';
					throw new Syx_Application_Exception('Log writer class not provided in options');
				}
				$class = Syx::loadClass($writer['class']);
				$args = array_key_exists('args', $writer) ? $writer['args'] : null;
				if (empty($args)) {
					$writer = new $class();
				} else {
					if (is_array($args) && (array_keys($args) === range(0, count($args) - 1))) {
						$rf = new ReflectionClass($class);
						$writer = $rf->newInstanceArgs($args);
					} else {
						$writer = new $class($args);
					}
				}
			}
			$Log->addWriter($writer);
		}
		return $this;
	}

	/**
	 * set Db settings
	 *
	 * @param array $options
	 * @return Syx_Application
	 */
	public function setDb(array $options)
	{
		if (!empty($options['adapter'])) {
			$db = Syx_Db::factory($options['adapter'], $options['params']);
			Syx_Db::setDefaultAdapter($db);
		}
		if (!empty($options['cache'])) {
			$cache = Syx_Cache::factory($options['cache']['adapter'], $options['cache']['options']);
			Syx_Db::setCacheAdapter($cache);
		}
		return $this;
	}

	/**
	 * set Session settings
	 *
	 * @param array $options
	 *
	 * @throws Syx_Application_Exception
	 * @return Syx_Application
	 */
	public function setSession(array $options)
	{
		if (!empty($options['saveHandler'])) {
			$saveHandler = $options['saveHandler'];
			if (is_array($saveHandler)) {
				if (!array_key_exists('class', $saveHandler)) {
					require_once 'Syx/Application/Exception.php';
					throw new Syx_Application_Exception('Session save handler class not provided in options');
				}
				$config = array_key_exists('options', $saveHandler) ? $saveHandler['options'] : null;
				$saveHandler = $saveHandler['class'];
				Syx::loadClass($saveHandler);
				$saveHandler = new $saveHandler($config);
			} elseif (is_string($saveHandler)) {
				Syx::loadClass($saveHandler);
				$saveHandler = new $saveHandler();
			}

			if (!$saveHandler instanceof Syx_Session_SaveHandler) {
				require_once 'Syx/Application/Exception.php';
				throw new Syx_Application_Exception('Invalid session save handler');
			}
		}
		if (!empty($options['options'])) {
			require_once 'Syx/Session.php';
			Syx_Session::setOptions($options['options']);
		}
		return $this;
	}

	public function setControllerPaths($paths)
	{
		if (is_array($paths)) {
			$this->_controllerPaths = implode(PATH_SEPARATOR, $paths);
		} else {
			$this->_controllerPaths = (string)$paths;
		}
	}

	/**
	 * set controller suffix
	 *
	 * @param string $suffix
	 *
	 * @throws Syx_Application_Exception
	 */
	public function setControllerSuffix($suffix)
	{
		if (preg_match('/[\W_]/', $suffix)) {
			require_once 'Syx/Application/Exception.php';
			throw new Syx_Application_Exception("controller suffix '$suffix' is not allowed");
		}
		$this->_controllerSuffix = ucfirst($suffix);
	}

	/**
	 * set ControllerKey
	 *
	 * @param string $key
	 * @return Syx_Application
	 */
	public function setControllerKey($key)
	{
		$this->_controllerKey = (string)$key;
		return $this;
	}

	/**
	 * set ActionKey
	 *
	 * @param string $key
	 * @return Syx_Application
	 */
	public function setActionKey($key)
	{
		$this->_actionKey = (string)$key;
		return $this;
	}

	/**
	 * set DefaultController
	 *
	 * @param string $value
	 * @return Syx_Application
	 */
	public function setDefaultController($value)
	{
		$this->_defaultController = $value;
		return $this;
	}

	/**
	 * set DefaultAction
	 *
	 * @param string $value
	 * @return Syx_Application
	 */
	public function setDefaultAction($value)
	{
		$this->_defaultAction = $value;
		return $this;
	}

	/**
	 * set Syx_Request_Abstract object
	 *
	 * @param string|Syx_Request_Abstract $request
	 * @return Syx_Application
	 * @throws Syx_Application_Exception
	 */
	public function setRequest($request)
	{
		if (is_string($request)) {
			Syx::loadClass($request);
			$request = new $request();
		}
		if (!$request instanceof Syx_Request_Abstract) {
			require_once 'Syx/Application/Exception.php';
			throw new Syx_Application_Exception('Invalid response class');
		}

		$this->_request = $request;

		return $this;
	}

	/**
	 * retrieve Syx_Request_Abstract object
	 *
	 * @return Syx_Request_Abstract
	 */
	public function getRequest()
	{
		if (null == $this->_request) {
			require_once 'Syx/Request/Http.php';
			$this->_request = new Syx_Request_Http();
		}
		$this->_request->setControllerKey($this->_controllerKey);
		$this->_request->setActionKey($this->_actionKey);
		return $this->_request;
	}

	/**
	 * set Syx_Response_Abstract object
	 *
	 * @param string|Syx_Response_Abstract $response
	 * @return Syx_Application
	 * @throws Syx_Application_Exception
	 */
	public function setResponse($response)
	{
		if (is_string($response)) {
			Syx::loadClass($response);
			$response = new $response();
		}
		if (!$response instanceof Syx_Response_Abstract) {
			require_once 'Syx/Application/Exception.php';
			throw new Syx_Application_Exception('Invalid response class');
		}

		$this->_response = $response;

		return $this;
	}

	/**
	 * retrieve Syx_Response_Abstract object
	 *
	 * @return Syx_Response_Abstract
	 */
	public function getResponse()
	{
		if (null == $this->_response) {
			require_once 'Syx/Response/Http.php';
			$this->_response = new Syx_Response_Http();
		}
		return $this->_response;
	}

	/**
	 * set route rules
	 *
	 * @param array $routes
	 * @return Syx_Application
	 */
	public function setRoutes(array $routes)
	{
		$this->_routes = $routes;
		return $this;
	}

	/**
	 * retrieve route rules
	 *
	 * @return array
	 */
	public function getRoutes()
	{
		return $this->_routes;
	}

	/**
	 * retrieve controller name
	 *
	 * @return string
	 */
	public function getControllerName()
	{
		$controller = $this->_request->getControllerName();
		if (!$controller && $this->_defaultController) {
			$controller = $this->_defaultController;
		}

		$controller = preg_replace('/[^a-z0-9 ]/i', '', str_replace('_', ' ', $controller));
		$controller = str_replace(' ', '_', ucwords($controller));
		$this->_request->setControllerName($controller);
		return $controller;
	}

	/**
	 * retrieve action name
	 *
	 * @return string
	 */
	public function getActionName()
	{
		$action = $this->_request->getActionName();
		if (!$action && $this->_defaultAction) {
			$action = $this->_defaultAction;
		}
		$action = strtolower($action);
		$this->_request->setActionName($action);
		return $action;
	}

	/**
	 * set all Options
	 *
	 * @param array $options
	 * @return Syx_Application
	 */
	public function setOptions(array $options)
	{
		foreach ($this->_options as $key) {
			if (isset($options[$key])) {
				$this->{'set' . ucfirst($key)}($options[$key]);
			}
		}
		return $this;
	}

	/**
	 * run application
	 *
	 * @param null|Syx_Request_Abstract  $request
	 * @param null|Syx_Response_Abstract $response
	 *
	 * @throws Syx_Application_Exception
	 * @return void
	 */
	public function run(Syx_Request_Abstract $request = null, Syx_Response_Abstract $response = null)
	{
		/**
		 * Instantiate default request object (HTTP version) if none provided
		 */
		if (null !== $request) {
			$this->setRequest($request);
		}
		$request = $this->getRequest();

		/**
		 * Instantiate default response object (HTTP version) if none provided
		 */
		if (null !== $response) {
			$this->setResponse($response);
		}
		$response = $this->getResponse();

		/**
		 * Initialize router
		 */
		if ($routes = $this->getRoutes()) {
			$router = new Syx_Router();
			$router->addRoute($routes);
			$router->route($request);
		}


		$controllerName = $this->getControllerName();
		$actionName = $this->getActionName();

		$className = $controllerName . $this->_controllerSuffix;
		Syx::loadClass($className, $this->_controllerPaths);

		// unset configure keys for application only
		foreach ($this->_options as $key) {
			unset($this->_config[$key]);
		}

		// instance controller
		$controller = new $className($request, $response, $this->_config);
		if (!$controller instanceof Syx_Controller) {
			require_once 'Syx/Application/Exception.php';
			throw new Syx_Application_Exception(
				"Controller '$className' is not an instance of Syx_Controller"
			);
		}

		// dispatching
		$controller->_dispatch($actionName);

		// send response to client
		$response->send();
	}
}