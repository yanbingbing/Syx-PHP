<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

require_once 'Syx/Route/Interface.php';

/**
 * Syx_Route_Regex
 *
 * @category Syx
 * @package  Syx_Route
 */
class Syx_Route_Regex implements Syx_Route_Interface
{
	/**
	 * @var string regexp of rule
	 */
	protected $_regex = null;

	/**
	 * @var array default values
	 */
	protected $_defaults = array();

	/**
	 * @var array map of matched keys to real keys
	 */
	protected $_map = array();

	/**
	 * @var bool need prefix httphost to uri
	 */
	protected $_hashost;

	public function __construct(array $config)
	{
		if (empty($config['rule'])) {
			require_once 'Syx/Route/Exception.php';
			throw new Syx_Route_Exception('Syx_Route_Regex need options "rule"');
		}
		$this->_regex = (string)$config['rule'];
		$this->_map = isset($config['map']) ? (array)$config['map'] : array();
		$this->_defaults = isset($config['defaults'])
			? $this->_getMappedValues((array)$config['defaults'])
			: array();
		$this->_hashost = !empty($config['hashost']);
	}

	/**
	 * match request(Syx_Request_Abstract) to this route
	 *
	 * @param Syx_Request_Abstract $request
	 *
	 * @return array|bool
	 */
	public function match(Syx_Request_Abstract $request)
	{
		if ($this->_hashost) {
			$path = $request->getHttpHost() . $request->getBasePath() . $request->getPathInfo();
			$path = trim(urldecode($path), Syx_Route_Interface::URI_DELI);
			$regex = '#^' . $this->_regex . '$#i';
		} else {
			$path = $request->getPathInfo();
			$regex = '#^' . $this->_regex . '#i';
		}

		$res = preg_match($regex, $path, $values);

		if (!$res) {
			return false;
		}

		unset($values[0]);

		$values = $this->_getMappedValues($values);
		$return = $values + $this->_defaults;
		return $return;
	}

	protected function _getMappedValues($values)
	{
		if (count($this->_map) == 0) {
			return $values;
		}
		$return = array();
		foreach ($values as $key => $value) {
			if (is_int($key)) {
				if (array_key_exists($key, $this->_map)) {
					$index = $this->_map[$key];
				} elseif (false === ($index = array_search($key, $this->_map))) {
					$index = $key;
				}
				$return[$index] = $values[$key];
			} else {
				$return[$key] = $value;
			}
		}
		return $return;
	}
}