<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Session_SaveHandler
 *
 * @category   Syx
 * @package    Syx_Session
 * @subpackage SaveHandler
 */
interface Syx_Session_SaveHandler
{
	/**
	 * Open Session - retrieve resources
	 *
	 * @param string $save_path
	 * @param string $name
	 */
	public function open($save_path, $name);

	/**
	 * Close Session - free resources
	 *
	 */
	public function close();

	/**
	 * Read session data
	 *
	 * @param string $id
	 */
	public function read($id);

	/**
	 * Write Session - commit data to resource
	 *
	 * @param string $id
	 * @param mixed  $data
	 */
	public function write($id, $data);

	/**
	 * Destroy Session - remove data from resource for
	 * given session id
	 *
	 * @param string $id
	 */
	public function destroy($id);

	/**
	 * Garbage Collection - remove old session data older
	 * than $maxlifetime (in seconds)
	 *
	 * @param int $maxlifetime
	 */
	public function gc($maxlifetime);
}