<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Router
 *
 * @category  Syx
 * @package   Syx_Router
 */
class Syx_Router
{

	/**
	 * array rules of routes
	 *
	 * @var array
	 */
	protected $_routes = array();

	/**
	 * add a single route of type Syx_Router
	 *
	 * @param string|array                   $name
	 * @param Syx_Route_Interface|array|null $route
	 *
	 * @return Syx_Router fluent interface
	 */
	public function addRoute($name, $route = null)
	{
		if (is_array($name)) {
			foreach ($name as $key => $route) {
				$this->addRoute($key, $route);
			}
		} elseif ($route instanceof Syx_Route_Interface) {
			$this->_routes[] = $route;
		} else {
			$class = isset($route['type']) ? $route['type'] : 'Syx_Route_Route';
			Syx::loadClass($class);
			$this->_routes[] = new $class($route);
		}
		return $this;
	}

	/**
	 * routing... match request(Syx_Request_Abstract) to all routes, parsing and get param data
	 *
	 * @param Syx_Request_Abstract $request
	 *
	 * @return void
	 */
	public function route(Syx_Request_Abstract $request)
	{
		foreach (array_reverse($this->_routes) as $route) {
			if (($params = $route->match($request)) != false) {
				$request->setParams($params);
				break;
			}
		}
	}
}
