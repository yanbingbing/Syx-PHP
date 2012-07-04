<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

require_once 'Syx/Auth/Storage/Interface.php';

require_once 'Syx/Session.php';

/**
 * Syx_Auth_Storage_Session
 *
 * @category   Syx
 * @package    Syx_Auth
 * @subpackage Storage
 */
class Syx_Auth_Storage_Session implements Syx_Auth_Storage_Interface
{
	/**
	 * Session object member
	 *
	 * @var mixed
	 */
	protected $_member;

	protected $_session;

	public function __construct($namespace = 'Syx_Auth', $member = 'storage')
	{
		Syx_Session::start();
		if (!isset($_SESSION[$namespace])) {
			$_SESSION[$namespace] = array();
		}
		$this->_session = & $_SESSION[$namespace];
		$this->_member = $member;
	}

	/**
	 * Defined by Syx_Auth_Storage_Interface
	 *
	 * @return boolean
	 */
	public function isEmpty()
	{
		return !isset($this->_session[$this->_member]);
	}

	/**
	 * Defined by Syx_Auth_Storage_Interface
	 *
	 * @return mixed
	 */
	public function read()
	{
		return isset($this->_session[$this->_member]) ? $this->_session[$this->_member] : null;
	}

	/**
	 * Defined by Syx_Auth_Storage_Interface
	 *
	 * @param  mixed $contents
	 *
	 * @return void
	 */
	public function write($contents)
	{
		$this->_session[$this->_member] = $contents;
	}

	/**
	 * Defined by Syx_Auth_Storage_Interface
	 *
	 * @return void
	 */
	public function clear()
	{
		unset($this->_session[$this->_member]);
	}
}