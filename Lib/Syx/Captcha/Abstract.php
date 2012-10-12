<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Captcha_Abstract
 *
 * @category  Syx
 * @package   Syx_Captcha
 */
abstract class Syx_Captcha_Abstract
{

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
	 * init captcha options
	 *
	 * @param array $options
	 *
	 * @return Syx_Captcha_Abstract
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
	 * get contentType of captcha
	 *
	 * @return string
	 */
	abstract public function getContentType();

	/**
	 * output captcha
	 *
	 * @param boolean $return
	 *
	 * @return string output if set $return true
	 */
	abstract public function render($return = false);
}