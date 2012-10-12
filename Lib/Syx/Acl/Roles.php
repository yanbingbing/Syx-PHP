<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Acl_Roles
 *
 * @category  Syx
 * @package   Syx_Acl
 */
class Syx_Acl_Roles implements SeekableIterator, Countable, ArrayAccess
{
	/**
	 * The original data for each row.
	 *
	 * @var array
	 */
	protected $_data;

	/**
	 * Syx_Acl_User_Interface object.
	 *
	 * @var Syx_Acl_User_Interface
	 */
	protected $_user;

	/**
	 * Syx_Acl_Role_Abstract class name.
	 *
	 * @var string
	 */
	protected $_roleClass = 'Syx_Acl_Role_Abstract';

	/**
	 * Iterator pointer.
	 *
	 * @var integer
	 */
	protected $_pointer = 0;

	/**
	 * How many data rows there are.
	 *
	 * @var integer
	 */
	protected $_count;

	/**
	 * Collection of instantiated Syx_Acl_Role_Abstract objects.
	 *
	 * @var array
	 */
	protected $_roles = array();


	public function __construct(Syx_Acl_User_Interface $user, array $data)
	{
		$this->_user = $user;
		$this->_roleClass = $user->getRoleClass();
		$this->_data = $data;
		$this->_count = count($this->_data);
	}

	public function rewind()
	{
		$this->_pointer = 0;
		return $this;
	}

	/**
	 * Required by interface Iterator.
	 *
	 * @return Syx_Acl_Role_Abstract
	 */
	public function current()
	{
		if ($this->valid() === false) {
			return null;
		}
		return $this->_initRole($this->_pointer);
	}

	public function key()
	{
		return $this->_pointer;
	}

	public function next()
	{
		++$this->_pointer;
	}

	public function valid()
	{
		return $this->_pointer < $this->_count;
	}

	public function count()
	{
		return $this->_count;
	}

	public function seek($position)
	{
		$position = (int)$position;
		if ($position < 0 || $position >= $this->_count) {
			require_once 'Syx/Acl/Exception.php';
			throw new Syx_Acl_Exception("Illegal index $position");
		}
		$this->_pointer = $position;
		return $this;
	}

	public function offsetExists($offset)
	{
		return isset($this->_data[(int)$offset]);
	}

	/**
	 * Required by the ArrayAccess implementation
	 *
	 * @return Syx_Acl_Role_Abstract
	 */
	public function offsetGet($offset)
	{
		$this->seek($offset);

		return $this->current();
	}

	public function offsetSet($offset, $value)
	{
	}

	public function offsetUnset($offset)
	{
	}

	protected function _initRole($pos)
	{
		if (!isset($this->_data[$pos])) {
			require_once 'Syx/Acl/Exception.php';
			throw new Syx_Acl_Exception("Data for provided position does not exist");
		}

		if (empty($this->_roles[$pos])) {
			Syx::loadClass($this->_roleClass);
			$this->_roles[$pos] = new $this->_roleClass($this->_user, $this->_data[$pos]);
		}

		return $this->_roles[$pos];
	}
}