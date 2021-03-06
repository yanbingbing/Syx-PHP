<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Auth_Storage_Interface
 *
 * @category   Syx
 * @package    Syx_Auth
 * @subpackage Storage
 */
Interface Syx_Auth_Storage_Interface
{
	/**
	 * Returns true if and only if storage is empty
	 *
	 * @return boolean
	 */
	public function isEmpty();

	/**
	 * Returns the contents of storage
	 *
	 * Behavior is undefined when storage is empty.
	 *
	 * @return mixed
	 */
	public function read();

	/**
	 * Writes $contents to storage
	 *
	 * @param  mixed $contents
	 *
	 * @return void
	 */
	public function write($contents);

	/**
	 * Clears contents from storage
	 *
	 * @return void
	 */
	public function clear();
}