<?php
/**
 * Xms project
 *
 * @copyright Copyright (c) 2011-2012 kakalong (http://yanbingbing.com)
 */

class Xms_Acl_User extends Syx_Acl_User_DbTable
{
	protected $_tableName = 'user_has_role';

	protected $_roleClass = 'Xms_Acl_Role';

	protected $_userColumn = 'userid';

	protected $_roleColumn = 'roleid';
}