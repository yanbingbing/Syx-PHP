<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Config
 */
require_once 'Syx/Config.php';

/**
 * Syx_Application
 */
require_once 'Syx/Application.php';


/**
 * Syx_Platform
 *
 * @category  Syx
 * @package   Syx_Platform
 */
class Syx_Platform
{
	/**
	 * global configure of platform
	 *
	 * @var Syx_Config
	 */
	protected $_config = null;

	/**
	 * default application name
	 *
	 * @var string
	 */
	protected $_defaultAppName = '';

	/**
	 * the key to access request retrieve appName
	 *
	 * @var string
	 */
	protected $_appKey = 'app';

	/**
	 * current name of app to run
	 *
	 * @var string
	 */
	protected $_appName = null;

	/**
	 * applications base path in absolute
	 *
	 * @var string
	 */
	protected $_appBasePath = '';

	/**
	 * config file of app relatived appPath
	 *
	 * @var string
	 */
	protected $_appConfigFile = 'Config/Config.php';

	/**
	 * array or separator by PATH_SEPARATOR Controller paths relatived appPath
	 *
	 * @var array|string
	 */
	protected $_controllerPaths = 'Controller';

	/**
	 * array or separator by PATH_SEPARATOR extra library paths relatived appPath
	 *
	 * @var array|string
	 */
	protected $_includePaths = array('Model');

	/**
	 * default view base path for auto find view file
	 *
	 * @var string
	 */
	protected $_defaultViewBasePath = 'View';

	/**
	 * route rules for Syx_Router
	 *
	 * @var null|array
	 */
	protected $_routes = null;

	/**
	 * the options can configure
	 *
	 * @var array
	 */
	protected $_options = array(
		'defaultAppName', 'appKey', 'appBasePath', 'appConfigFile',
		'controllerPaths', 'includePaths', 'defaultViewBasePath', 'routes'
	);

	/**
	 * Options:
	 *  defaultAppName  => default application name
	 *  appKey          => the key access request object retrieve appName
	 *  appBasePath     => applications base path in absolute
	 *  appConfigFile   => application config file path relatived appPath [Config/Config.php]
	 *  controllerPaths => application controller paths relatived appPath [Controller]
	 *  includePaths    => application include paths relatived appPath [Model:Plugin]
	 *  routes          => route rules
	 *
	 * @param string|array|Syx_Config $options
	 */
	public function __construct($options = null)
	{
		$this->_config = new Syx_Config();
		if (is_string($options)) {
			$this->_config->load($options);
		} elseif (is_array($options)) {
			$this->_config->merge($options);
		} elseif (is_object($options) && method_exists($options, 'toArray')) {
			$this->_config->merge($options->toArray());
		}

		$options = $this->_config->toArray();
		if (!empty($options)) {
			$this->setOptions($options);
		}
	}

	/**
	 * set all options
	 * Options:
	 *  defaultAppName  => default application name
	 *  appKey          => the key access request object retrieve appName
	 *  appBasePath     => applications base path in absolute
	 *  appConfigFile   => application config file path relatived appPath [Config/Config.php]
	 *  controllerPaths => application controller paths relatived appPath [Controller]
	 *  includePaths    => application include paths relatived appPath [Model]
	 *  routes          => route rules
	 *
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
		foreach ($this->_options as $key) {
			if (isset($options[$key])) {
				$this->{'set' . ucfirst($key)}($options[$key]);
			}
		}
	}

	/**
	 * set route rules
	 *
	 * @param array $routes
	 */
	public function setRoutes(array $routes)
	{
		$this->_routes = $routes;
	}

	/**
	 * retrieve route rules
	 *
	 * @return array|null
	 */
	public function getRoutes()
	{
		return $this->_routes;
	}

	/**
	 * set controller loading paths
	 *
	 * each path relatived appPath
	 *
	 * @param string|array $paths string separator by PATH_SEPARATOR or array
	 */
	public function setControllerPaths($paths)
	{
		$this->_controllerPaths = $paths;
	}

	/**
	 * get controller paths in absolute
	 *
	 * @return array
	 */
	public function getControllerPaths()
	{
		if (!is_array($this->_controllerPaths)) {
			$this->_controllerPaths = explode(PATH_SEPARATOR, (string)$this->_controllerPaths);
		}
		$paths = array();
		foreach ($this->_controllerPaths as $path) {
			$paths[] = $this->getAppPath() . '/' . $path;
		}
		return $paths;
	}

	/**
	 * set include paths
	 *
	 * each path relatived appPath or absolute
	 *
	 * @param string|array $paths string separator by PATH_SEPARATOR or array
	 */
	public function setIncludePaths($paths)
	{
		$this->_includePaths = $paths;
	}

	/**
	 * get include paths in absolute
	 *
	 * @return array
	 */
	public function getIncludePaths()
	{
		if (!is_array($this->_includePaths)) {
			$this->_includePaths = explode(PATH_SEPARATOR, (string)$this->_includePaths);
		}
		$paths = array();
		foreach ($this->_includePaths as $path) {
			$paths[] = $this->_isAbsolute($path)
				? $path
				: ($this->getAppPath() . '/' . $path);
		}
		return $paths;
	}

	/**
	 * set default view base path for auto find views
	 *
	 * @param string $path relatived appPath or in absolute
	 */
	public function setDefaultViewBasePath($path)
	{
		$this->_defaultViewBasePath = $path;
	}

	/**
	 * get default view base path for auto find views
	 *
	 * @reuturn string absolute path
	 */
	public function getDefaultViewBasePath()
	{
		return $this->_isAbsolute($this->_defaultViewBasePath)
			? $this->_defaultViewBasePath
			: ($this->getAppPath() . '/' . $this->_defaultViewBasePath);
	}

	/**
	 * test a path if absolute
	 *
	 * @param string $path
	 *
	 * @return int
	 */
	protected function _isAbsolute($path)
	{
		return preg_match('~^([A-Z]\:[/\\\]|/)~i', $path);
	}

	/**
	 * set default application name
	 *
	 * @param string $name
	 */
	public function setDefaultAppName($name)
	{
		$this->_defaultAppName = $name;
	}

	/**
	 * get default application name
	 *
	 * @return string
	 */
	public function getDefaultAppName()
	{
		return $this->_defaultAppName;
	}

	/**
	 * set the key for retrieving application name from request
	 *
	 * @param string $key
	 *
	 * @throws Syx_Platform_Exception
	 */
	public function setAppKey($key)
	{
		if (empty($key)) {
			require_once 'Syx/Platform/Exception.php';
			throw new Syx_Platform_Exception("empty for appKey");
		}
		$this->_appKey = (string)$key;
	}

	/**
	 * get app key for retrieving application name from request
	 *
	 * @return string
	 */
	public function getAppKey()
	{
		return $this->_appKey;
	}

	/**
	 * set the application name
	 *
	 * @param string $name
	 */
	public function setAppName($name)
	{
		$this->_appName = $name;
	}

	/**
	 * retrieve application name
	 *
	 * @return string
	 */
	public function getAppName()
	{
		if (empty($this->_appName)) {
			$this->_appName = $this->getDefaultAppName();
		}

		$app = preg_replace('/[^a-z0-9 ]/i', '', str_replace('_', ' ', $this->_appName));
		return str_replace(' ', '', ucwords($app));
	}

	/**
	 * set applications base path in absolute
	 *
	 * @param string $basePath
	 */
	public function setAppBasePath($basePath)
	{
		$this->_appBasePath = $basePath;
	}

	/**
	 * set application config file path relatived appPath
	 *
	 * @param string $file
	 */
	public function setAppConfigFile($file)
	{
		$this->_appConfigFile = $file;
	}

	/**
	 * get application config file path in absolute
	 *
	 * @return null|string
	 */
	public function getAppConfigFile()
	{
		if (!empty($this->_appConfigFile)) {
			$file = $this->_isAbsolute($this->_appConfigFile)
				? $this->_appConfigFile
				: ($this->getAppPath() . '/' . $this->_appConfigFile);
			return is_file($file) ? $file : null;
		}
		return null;
	}

	/**
	 * get current application path
	 *
	 * must set the applications base path and application name before use
	 *
	 * @throws Syx_Platform_Exception
	 * @return string
	 */
	public function getAppPath()
	{
		$appName = $this->getAppName();
		if (empty($this->_appBasePath) || empty($appName)) {
			require_once 'Syx/Platform/Exception.php';
			throw new Syx_Platform_Exception('applications base path and application name must assign before use');
		}
		return $this->_appBasePath . '/' . $appName;
	}

	/**
	 * get a application instance
	 *
	 * @param null|Syx_Request_Abstract $request
	 * @param null                      $reponse
	 *
	 * @throws Syx_Platform_Exception
	 * @return Syx_Application
	 */
	public function getApplication(Syx_Request_Abstract $request = null, $reponse = null)
	{
		// if $request null given, default to Syx_Request_Http
		if (null == $request) {
			$request = new Syx_Request_Http();
		}

		$request->setAppKey($this->getAppKey());

		// route the request
		if ($routes = $this->getRoutes()) {
			$router = new Syx_Router();
			$router->addRoute($routes);
			$router->route($request);
		}

		// set app name
		$this->setAppName($request->getAppName());
		$appName = $this->getAppName();
		if (empty($appName)) {
			require_once 'Syx/Platform/Exception.php';
			throw new Syx_Platform_Exception("Expect appname, null or unsafe value given");
		}
		$request->setAppName($appName);

		// define two very useful constant
		define('APP_NAME', $this->getAppName());
		define('APP_PATH', $this->getAppPath());

		// unset configure keys for platform only
		foreach ($this->_options as $key) {
			unset($this->_config[$key]);
		}

		$this->_config['defaultViewBasePath'] = $this->getDefaultViewBasePath();

		// merge the application configure
		if ($file = $this->getAppConfigFile()) {
			$this->_config->load($file);
		}

		// create application
		$app = new Syx_Application($this->_config);

		$app->setControllerPaths($this->getControllerPaths());
		$app->setIncludePaths($this->getIncludePaths());

		$app->setRequest($request);

		if (null !== $reponse) {
			$app->setResponse($reponse);
		}

		return $app;
	}
}