<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Acl
 *
 * @category Syx
 * @package  Syx_Acl
 */
abstract class Syx_Acl
{
	/**
	 * check that is allowed
	 *
	 * @param Syx_Acl_User_Interface     $user
	 * @param Syx_Acl_Resource_Interface $resource
	 *
	 * @return boolean
	 */
	public static function isAllowed(Syx_Acl_User_Interface $user, Syx_Acl_Resource_Interface $resource)
	{
		foreach ($user->getRoles() as $role) {
			if ($role->hasResource($resource)) {
				return true;
			}
		}
		return false;
	}
}