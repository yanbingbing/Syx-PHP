<?php
/**
 * Xms project
 *
 * @copyright Copyright (c) 2011-2012 kakalong (http://yanbingbing.com)
 */

class Xms_Admin_Controller extends Syx_Controller
{

	protected function _init()
	{
		$storage = new Syx_Auth_Storage_Session('New_Admin', 'login');
		if (!($login = $storage->read())) {
			$this->_response->setRedirect('/System/Auth/login');
			$this->_response->send();
		}

		if (!($db = Syx_Db::getDefaultAdapter())) {
			throw new Xms_Exception('A db connection was not provided.');
		}

		$user = new Xms_Acl_User($db, $login['userid']);
		Syx::set('user', $user);

		$resource = new Xms_Acl_Resource_Action($db, $this->_request->getAppName(),
			$this->_request->getControllerName(),
			$this->_request->getActionName());

		if (!Syx_Acl::isAllowed($user, $resource)) {
			$this->showMessage('无权限');
			return;
		}
	}

	protected function showMessage($msg)
	{
		echo $msg;
	}
}