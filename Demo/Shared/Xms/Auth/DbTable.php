<?php
/**
 * Xms project
 *
 * @copyright Copyright (c) 2011-2012 kakalong (http://yanbingbing.com)
 */

class Xms_Auth_DbTable extends Syx_Auth_Adapter_DbTable
{
	protected $_tableName = 'user';

	/**
	 * $_identityColumn - the column to use as the identity
	 *
	 * @var string
	 */
	protected $_identityColumn = 'email';

	/**
	 * $_credentialColumns - columns to be used as the credentials
	 *
	 * @var string
	 */
	protected $_credentialColumn = 'password';

	protected $_fields = 'userid, groupid, email, username, disabled';

	public function __construct(Syx_Db_Adapter $adapter, Syx_Request_Abstract $request)
	{
		parent::__construct($adapter,
			$request->get($this->_identityColumn),
			md5($request->get($this->_credentialColumn)));
	}
}