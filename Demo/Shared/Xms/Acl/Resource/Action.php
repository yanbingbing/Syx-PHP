<?php
/**
 * Xms project
 *
 * @copyright Copyright (c) 2011-2012 kakalong (http://yanbingbing.com)
 */

class Xms_Acl_Resource_Action implements Syx_Acl_Resource_Interface
{
	protected $_public = false;

	protected $_stmt = null;

	public function __construct(Syx_Db_Adapter $db, $application, $controller, $action)
	{
		$select = $db->select()->from('action_has_access', 'actionid')->where('access', array(
			strtolower("$application"),
			strtolower("$application.$controller"),
			strtolower("$application.$controller.$action")
		));
		$actids = $select->query()->fetchAll(PDO::FETCH_COLUMN, 0);

		if (empty($actids)) {
			return;
		}
		$this->_public = $db->select()->from('action', E('count(*)'))->where('actionid', $actids)
			->where('public=1')->query()->fetchColumn();
		if (!$this->_public) {
			$this->_stmt = $db->select()->from('role_has_action', E('count(*)'))
				->where('actionid', $actids)->where('roleid=?')->prepare(Syx_Db::FETCH_NUM);
		}
	}

	public function isAllowRole($roleid)
	{
		if ($this->_public) {
			return true;
		} else if ($this->_stmt) {
			$this->_stmt->execute(array($roleid));
			return $this->_stmt->fetchColumn();
		}
		return false;
	}
}