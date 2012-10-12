<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Auth
 *
 * @category  Syx
 * @package   Syx_Auth
 */
class Syx_Auth
{
	protected static $_instance = null;

	/**
	 * storage of auth result
	 *
	 * @var Syx_Auth_Storage_Interface
	 */
	protected $_storage = null;

	/**
	 * Singleton pattern implementation makes "new" unavailable
	 */
	protected function __construct()
	{
	}

	/**
	 * Returns an instance of Syx_Auth
	 *
	 * Singleton pattern implementation
	 *
	 * @return Syx_Auth
	 */
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * get storage of auth result
	 *
	 * @return Syx_Auth_Storage_Interface
	 */
	public function getStorage()
	{
		if (null === $this->_storage) {
			include_once 'Syx/Auth/Storage/Session.php';
			$this->setStorage(new Syx_Auth_Storage_Session());
		}
		return $this->_storage;
	}

	/**
	 * set storage of auth result
	 *
	 * @param Syx_Auth_Storage_Interface $storage
	 */
	public function setStorage(Syx_Auth_Storage_Interface $storage)
	{
		$this->_storage = $storage;
	}

	/**
	 * Authenticates against the supplied adapter
	 *
	 * @param  Syx_Auth_Adapter_Interface $adapter
	 *
	 * @return Syx_Auth_Result
	 */
	public function authenticate(Syx_Auth_Adapter_Interface $adapter)
	{
		$result = $adapter->authenticate();

		if ($result->isValid()) {
			$this->getStorage()->write($result->getIdentity());
		}

		return $result;
	}

	/**
	 * Returns true if and only if an identity is available from storage
	 *
	 * @return boolean
	 */
	public function hasIdentity()
	{
		return !$this->getStorage()->isEmpty();
	}

	/**
	 * Returns the identity from storage or null if no identity is available
	 *
	 * @return mixed|null
	 */
	public function getIdentity()
	{
		$storage = $this->getStorage();

		if ($storage->isEmpty()) {
			return null;
		}

		return $storage->read();
	}

	/**
	 * Clears the identity from persistent storage
	 *
	 * @return void
	 */
	public function clearIdentity()
	{
		$this->getStorage()->clear();
	}
}