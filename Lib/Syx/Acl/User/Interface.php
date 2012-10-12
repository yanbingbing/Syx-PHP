<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Acl_User_Interface
 *
 * @category   Syx
 * @package    Syx_Acl
 * @subpackage User
 */
interface Syx_Acl_User_Interface
{
	public function setRoleClass($class);

	public function getRoleClass();

	public function getRoles();
}