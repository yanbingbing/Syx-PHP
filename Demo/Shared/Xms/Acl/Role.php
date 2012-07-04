<?php
/**
 * Xms project
 *
 * @copyright Copyright (c) 2011-2012 kakalong (http://yanbingbing.com)
 */

class Xms_Acl_Role extends Syx_Acl_Role_Abstract
{
	protected $_db;

	public function __construct(Syx_Acl_User_DbTable $user, $identity)
	{
		parent::__construct($user, $identity);
		$this->_db = $user->getAdapter();
	}

	public function hasResource(Syx_Acl_Resource_Interface $resource)
	{
		$roleid = $this->_identity;

		$select = new Syx_Db_Select($this->_db);
		$stmt = $select->from('role', 'parentid')->where('roleid=?')
			->prepare(Syx_Db::FETCH_NUM);
		while (1) {
			if ($resource->isAllowRole($roleid)) {
				return true;
			}

			$stmt->execute(array($roleid));
			$roleid = $stmt->fetchColumn();
			if (empty($roleid)) {
				return false;
			}
		}

		return false;
	}
}