<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

require_once 'Syx/View/Abstract.php';

/**
 * Syx_View_Twig
 *
 * @category  Syx
 * @package   Syx_View
 */
class Syx_View_Twig extends Syx_View_Abstract
{
	/**
	 * Twig Template Engine
	 *
	 * @var Twig_Environment
	 */
	protected $_engine = null;

	/**
	 * the template name
	 * @var string
	 */
	protected $_viewFile = '';

	public function __construct($options = null)
	{
		try {
			Syx::loadClass('Twig_Environment');
		} catch (Exception $e) {
			throw new Syx_View_Exception('Twig Template Engine was not installed');
		}
		$this->_engine = new Twig_Environment();
		parent::__construct($options);
	}

	/**
	 * @see Syx_View_Abstract
	 * @return object|Twig_Environment
	 */
	public function getEngine()
	{
		return $this->_engine;
	}

	/**
	 * @see Syx_View_Abstract
	 */
	public function setViewFile($file)
	{
		$this->_viewFile = $file;
		return $this;
	}

	/**
	 * set compiled cache path
	 *
	 * @param string $path
	 *
	 * @return Syx_View_Twig
	 */
	public function setCachePath($path)
	{
		$this->_engine->setCache($path);
		return $this;
	}

	/**
	 * @see Syx_View_Abstract
	 */
	public function setViewBasePath($path)
	{
		$this->_engine->setLoader(new Twig_Loader_Filesystem($path));
		return $this;
	}

	/**
	 * assign variables, render template
	 *
	 * @return string
	 */
	public function render()
	{
		$template = $this->_engine->loadTemplate($this->_viewFile);
		return $template->render($this->_varibles);
	}
}
