<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

require_once 'Syx/View/Abstract.php';

/**
 * Syx_View_Simple
 *
 * @category  Syx
 * @package   Syx_View
 */
class Syx_View_Simple extends Syx_View_Abstract
{
	/**
	 * where the original template placed
	 *
	 * @var string
	 */
	protected $_viewBasePath = '';

	/**
	 * the template script name
	 *
	 * @var string
	 */
	protected $_viewFile = '';

	/**
	 * set Script file
	 *
	 * @param string $file
	 *
	 * @return Syx_View_Abstract|Syx_View_Simple
	 */
	public function setViewFile($file)
	{
		$this->_viewFile = $file;
		return $this;
	}

	/**
	 * set Template file base directory
	 *
	 * @param string $dir
	 *
	 * @return Syx_View_Abstract|Syx_View_Simple
	 */
	public function setViewBasePath($dir)
	{
		$this->_viewBasePath = $dir;
		return $this;
	}

	/**
	 * assign variables, render template
	 *
	 * @throws Syx_View_Exception
	 * @return string
	 */
	public function render()
	{
		$__viewFile = $this->_viewBasePath . '/' . $this->_viewFile;
		if (!is_file($__viewFile)) {
			require_once 'Syx/View/Exception.php';
			throw new Syx_View_Exception("View file '$this->_viewFile' not found.");
		}
		// avoid find files in other paths
		$__origCwd = getcwd();
		chdir($this->_viewBasePath);
		ob_start();
		try {
			extract($this->_variables);
			require $__viewFile;
			$content = ob_get_clean();
			chdir($__origCwd);
		} catch (Exception $e) {
			ob_end_clean();
			chdir($__origCwd);
			require_once 'Syx/View/Exception.php';
			throw new Syx_View_Exception($e->getMessage(), $e->getCode());
		}
		return $content;
	}
}
