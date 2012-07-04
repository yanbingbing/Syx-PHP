<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

require_once 'Syx/Output/Abstract.php';

/**
 * Syx_Output_Captcha
 *
 * @category   Syx
 * @package    Syx_Output
 */
class Syx_Output_Captcha extends Syx_Output_Abstract
{
	/**
	 * Response object
	 *
	 * @var Syx_Response_Http
	 */
	protected $_response;

	/**
	 * the captcha render engine
	 *
	 * @var Syx_Captcha_Abstract
	 */
	protected $_captcha = null;

	/**
	 * set captcha of render engine
	 *
	 * @param string|array|Syx_Captcha_Abstract $captcha
	 *
	 * @return Syx_Output_Captcha
	 * @throws Syx_Output_Exception
	 */
	public function setCaptcha($captcha)
	{
		if (is_string($captcha)) {
			Syx::loadClass($captcha);
			$captcha = new $captcha();
		} elseif (is_array($captcha)) {
			if (!array_key_exists('class', $captcha)) {
				require_once 'Syx/Output/Exception.php';
				throw new Syx_Output_Exception('View class not provided in options');
			}
			$class = $captcha['class'];
			unset($captcha['class']);
			Syx::loadClass($class);
			$captcha = new $class($captcha);
		}
		if (!$captcha instanceof Syx_Captcha_Abstract) {
			require_once 'Syx/Output/Exception.php';
			throw new Syx_Output_Exception('Not valid type of captcha instance.');
		}
		$this->_captcha = $captcha;
		return $this;
	}

	/**
	 * retrieve captcha render engine
	 *
	 * @return Syx_Captcha_Abstract
	 */
	public function getCaptcha()
	{
		return $this->_captcha;
	}

	/**
	 * output body of captcha
	 *
	 * @return string
	 */
	public function outputBody()
	{
		if (null != $this->_captcha) {
			$this->_response->setHeader('Content-Type', $this->_captcha->getContentType())->header();
			$this->_captcha->render();
		}
	}
}