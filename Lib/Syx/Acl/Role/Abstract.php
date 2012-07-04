<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Acl_Role_Abstract
 *
 * @category  Syx
 * @package   Syx_Acl
 * @subpackage Role
 */
abstract class Syx_Acl_Role_Abstract
{
	protected $_user;

	protected $_identity;

	public function __construct(Syx_Acl_User_Interface $user, $identity)
	{
		$this->_identity = $identity;
		$this->_user = $user;
	}

	abstract public function hasResource(Syx_Acl_Resource_Interface $resource);
}