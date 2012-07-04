<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Output_Abstract
 *
 * @category  Syx
 * @package   Syx_Output
 */
abstract class Syx_Output_Abstract
{

	/**
	 * Response object
	 *
	 * @var Syx_Response_Abstract
	 */
	protected $_response;

	/**
	 * @param Syx_Response_Abstract $response
	 * @param array                 $options
	 */
	public function __construct(Syx_Response_Abstract $response, array $options = array())
	{
		$this->_response = $response;
		$this->setOptions($options);
	}

	/**
	 * init the options
	 *
	 * @param array $options
	 *
	 * @return Syx_Output_Abstract
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
	 * output the body content
	 *
	 * @return string
	 */
	abstract public function outputBody();
}