<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Controller
 *
 * @category  Syx
 * @package   Syx_Controller
 */
abstract class Syx_Controller
{
	/**
	 * suffix of template file
	 *
	 * @var string
	 */
	protected $_viewSuffix = 'phtml';

	/**
	 * default view base path
	 *
	 * @var string
	 */
	protected $_defaultViewBasePath = 'View';

	/**
	 * Request object wrapping the request environment
	 *
	 * @var Syx_Request_Http
	 */
	protected $_request;

	/**
	 * Response object wrapping the response
	 *
	 * @var Syx_Response_Http
	 */
	protected $_response;

	/**
	 * Output object control output
	 *
	 * @var Syx_Output_Abstract
	 */
	protected $_output = null;

	/**
	 * View object to render template
	 *
	 * @var Syx_View_Abstract
	 */
	protected $_view = null;

	final public function __construct(Syx_Request_Abstract $request, Syx_Response_Abstract $response, array $options = null)
	{
		$this->_request = $request;
		$this->_response = $response;
		if ($options) {
			$this->_setOptions($options);
		}
		$this->_init();
	}

	/**
	 * init the controller
	 */
	protected function _init()
	{
	}

	/**
	 * set options pass in
	 *
	 * @param array $options
	 */
	protected function _setOptions(array $options)
	{
		foreach ($options as $key => $val) {
			$method = '_set' . ucfirst($key);
			if (method_exists($this, $method)) {
				// Setter exists; use it
				$this->$method($val);
			}
		}
	}

	/**
	 * set the view suffix
	 *
	 * @param string $suffix
	 */
	protected function _setViewSuffix($suffix)
	{
		$this->_viewSuffix = $suffix;
	}

	/**
	 * Init a output object
	 *
	 * @param Syx_Output_Abstract|string $output
	 * @param array                      $options
	 *
	 * @throws Syx_Controller_Exception
	 * @return Syx_Output_Abstract
	 */
	protected function _setOutput($output, array $options = array())
	{
		if ($this->_output instanceof Syx_Output_Abstract) {
			return $this->_output->setOptions($options);
		}

		if ($output instanceof Syx_Output_Abstract) {
			$output->setOptions($options);
			return $this->_output = $output;
		}

		/*
		 * Verify that an adapter name has been specified.
		 */
		if (!is_string($output) || empty($output)) {
			require_once 'Syx/Controller/Exception.php';
			throw new Syx_Controller_Exception('Output name must be specified in a string');
		}

		/*
		 * Form full adapter class name
		 */
		$adapterNamespace = 'Syx_Output';
		if (isset($options['adapterNamespace'])) {
			if ($options['adapterNamespace'] != '') {
				$adapterNamespace = $options['adapterNamespace'];
			}
			unset($options['adapterNamespace']);
		}

		$adapterName = Syx::loadClass($adapterNamespace . '_' . strtolower($output));

		/*
		 * Create an instance of the adapter class.
		 * Pass the config to the adapter class constructor.
		 */
		$output = new $adapterName($this->_response, $options);

		/*
		 * Verify that the object created is a descendent of the abstract adapter type.
		 */
		if (!$output instanceof Syx_Output_Abstract) {
			require_once 'Syx/Controller/Exception.php';
			throw new Syx_Controller_Exception("Class '$adapterName' does not extend Syx_Output_Abstract");
		}

		return $this->_output = $output;
	}

	/**
	 * initilized the view object
	 *
	 * @param Syx_View_Abstract|string|array $view
	 *
	 * @throws Syx_Controller_Exception
	 */
	protected function _setView($view = null)
	{
		if (isset($this->_output) && !($this->_output instanceof Syx_Output_View)) {
			require_once 'Syx/Controller/Exception.php';
			throw new Syx_Controller_Exception('Current type of Output not support Syx_View');
		}
		$options = array();
		if ($view instanceof Syx_View_Abstract) {
			$options['view'] = $view;
		} else {
			$options['view'] = array(
				'class'        => 'Syx_View_Simple',
				'viewBasePath' => $this->_getDefaultViewBasePath(),
				'viewFile'     => $this->_getViewFile()
			);
			if (is_string($view)) {
				$options['view']['class'] = $view;
			} elseif (is_array($view)) {
				$options['view'] = array_merge($options['view'], $view);
			}
		}

		$this->_setOutput('view', $options);
		$this->_view = $this->_output->getView();
		$this->_view->assign('_INPUT', $this->_request);
	}

	/**
	 * get reference of view
	 *
	 * @return Syx_View_Abstract
	 */
	protected function _getView()
	{
		if (null == $this->_view) {
			$this->_setView();
		}
		return $this->_view;
	}

	/**
	 * assign variablea to view
	 *
	 * @param string|array $spec
	 * @param mixed        $value
	 *
	 * @return Syx_View_Abstract
	 */
	protected function assign($spec, $value = null)
	{
		return $this->_getView()->assign($spec, $value);
	}

	/**
	 * display the view content
	 *
	 * @param string $name template name
	 * @param string $dir  directory of the template
	 */
	protected function display($name = null, $dir = null)
	{
		$this->_getView()->setViewFile($this->_getViewFile($name, $dir));
	}

	/**
	 * check if has default view script
	 *
	 * @return bool
	 */
	protected function _hasDefaultView()
	{
		$viewfile = $this->_getDefaultViewBasePath() .'/'. $this->_getViewFile();
		return is_file($viewfile);
	}

	/**
	 * set default view base path
	 *
	 * @param string $path
	 */
	protected function _setDefaultViewBasePath($path)
	{
		$this->_defaultViewBasePath = $path;
	}

	/**
	 * retrieve default view base path
	 *
	 * @return string
	 */
	protected function _getDefaultViewBasePath()
	{
		return $this->_defaultViewBasePath;
	}

	/**
	 * get view template file
	 *
	 * @param string $name
	 * @param string $dir
	 *
	 * @return string
	 */
	protected function _getViewFile($name = null, $dir = null)
	{
		if (is_null($name)) {
			$name = $this->_request->getActionName();
		}
		if (is_null($dir)) {
			$dir = $this->_request->getControllerName();
		}
		$dir = preg_replace('/[^a-z0-9 ]/i', '',
			trim(str_replace(array('_', '/', '\\'), ' ', $dir)));
		$dir = str_replace(' ', '/', ucwords($dir));
		return $dir . '/' . strtolower($name) . '.' . $this->_viewSuffix;
	}

	/**
	 * transform action name map
	 *
	 * @param string $action
	 *
	 * @return string
	 */
	protected function _transformAction($action)
	{
		return $action;
	}

	/**
	 * For internal use, dispatch the action
	 *
	 * @param string $action
	 *
	 * @throws Exception
	 * @throws Syx_Controller_Exception
	 */
	public function _dispatch($action)
	{
		$action = $this->_transformAction($action);
		if (!method_exists($this, $action)) {
			require_once 'Syx/Controller/Exception.php';
			throw new Syx_Controller_Exception("Action '$action' not found.");
		} else {
			$obLevel = ob_get_level();
			ob_start();
			try {
				$return = $this->$action();
			} catch (Exception $e) {
				while (ob_get_level() > $obLevel) {
					ob_end_clean();
				}
				throw $e;
			}

			while (ob_get_level() - $obLevel > 1) {
				ob_end_flush();
			}
			$text = ob_get_clean();

			switch (true) {
			case is_int($return):
				if (100 > $return || 599 < $return) {
					$return = 200;
				}
				$this->_response->setHttpResponseCode($return);
				$this->_response->setBody($text);
				break;
			case is_string($return):
				$this->_response->setBody($return);
				break;
			case is_array($return):
				$return = json_encode($return);
				$this->_response->setBody(empty($_GET['jsoncallback']) ? $return : "{$_GET['jsoncallback']}($return)");
				break;
			case $this->_output instanceof Syx_Output_Abstract:
				$this->_response->setBody($this->_output);
				break;
			case $return instanceof Syx_Output_Abstract:
				$this->_response->setBody($return);
				break;
			case $text !== '':
				$this->_response->setBody($text);
				break;
			case $this->_hasDefaultView():
				$this->_setView();
				$this->_response->setBody($this->_output);
				break;
			}
		}
	}
}