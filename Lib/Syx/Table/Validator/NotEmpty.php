<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

require_once 'Syx/Table/Validator/Interface.php';

/**
 * Syx_Table_Validator_NotEmpty
 *
 * @category   Syx
 * @package    Syx_Table
 * @subpackage Validator
 */
class Syx_Table_Validator_NotEmpty implements Syx_Table_Validator_Interface
{
	/**
	 * Check if valid
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function isValid($value)
	{
		return !is_null($value) && $value !== '';
	}
}