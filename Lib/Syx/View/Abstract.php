<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

/**
 * Syx_View_Abstract
 *
 * @category  Syx
 * @package   Syx_View
 */
abstract class Syx_View_Abstract
{
	/**
	 * template variables
	 *
	 * @var array
	 */
	protected $_variables = array();

	/**
	 * @param array|Syx_Config $options
	 */
	public function __construct($options = null)
	{
		if (is_array($options)) {
			$this->setOptions($options);
		} elseif ($options instanceof Syx_Config) {
			$this->setOptions($options->toArray());
		}
	}

	/**
	 * setOptions
	 *
	 * @param array $options
	 *
	 * @return Syx_View_Abstract
	 */
	public function setOptions(array $options)
	{
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method)) {
				// Setter exists; use it
				$this->$method($value);
			}
		}
		return $this;
	}

	/**
	 * Return the template engine object, if any
	 *
	 * If using a third-party template engine, such as Smarty, patTemplate,
	 * phplib, etc, return the template engine object. Useful for calling
	 * methods on these objects, such as for setting filters, modifiers, etc.
	 *
	 * @return object
	 */
	public function getEngine()
	{
		return $this;
	}

	/**
	 * Assign a variable to the view
	 *
	 * @param string $key The variable name.
	 * @param mixed  $val The variable value.
	 *
	 * @return void
	 */
	public function __set($key, $val)
	{
		$this->_variables[$key] = $val;
	}

	/**
	 * Allows testing with empty() and isset() to work
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function __isset($key)
	{
		return isset($this->_variables[$key]);
	}

	/**
	 * Allows unset() on object properties to work
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	public function __unset($key)
	{
		unset($this->_variables[$key]);
	}

	/**
	 * Assign variables to the view script via differing strategies.
	 *
	 * @param string|array $spec  The assignment strategy to use (key or array of key
	 * => value pairs)
	 * @param mixed        $value (Optional) If assigning a named variable, use this
	 *                            as the value.
	 *
	 * @throws Syx_View_Exception
	 * @return Syx_View_Abstract
	 */
	public function assign($spec, $value = null)
	{
		if (is_string($spec)) {
			$this->__set($spec, $value);
		} elseif (is_array($spec)) {
			foreach ($spec as $k => $v) {
				$this->__set($k, $v);
			}
		} else {
			require_once 'Syx/View/Exception.php';
			throw new Syx_View_Exception('assign() expects a string or array, received ' . gettype($spec));
		}
	}

	/**
	 * Clear all assigned variables
	 *
	 * @return Syx_View_Abstract
	 */
	public function clearVars()
	{
		$this->_variables = array();
		return $this;
	}

	/**
	 * set view file
	 *
	 * $path is relative to templatebase or compliledbase
	 *
	 * @param string $file
	 *
	 * @return Syx_View_Abstract
	 */
	abstract public function setViewFile($file);

	/**
	 * set view file base path
	 *
	 * @param string $path
	 *
	 * @return Syx_View_Abstract
	 */
	abstract public function setViewBasePath($path);

	/**
	 * Processes a view script and returns the output.
	 *
	 * @return string The script output.
	 */
	abstract public function render();
}
