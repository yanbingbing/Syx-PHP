<?php
class AdminRecord extends Syx_Table_Record
{
	protected function _init()
	{
		$this->addValidator('name', 'NotEmpty', '名称不能为空')
			->setDefaultValue('createdby', 1, self::ON_INSERT)
			->setDefaultValue('roleid', 1, self::ON_INSERT)
			->setDefaultValue('departmentid', 1, self::ON_INSERT);
	}

	protected function _getName()
	{
		return 'My name is ' . $this->_data['name'];
	}

	protected function _fillUpdatedOnUpdate()
	{
		return time();
	}

	protected function _fillCreatedOnInsert()
	{
		return time();
	}
}