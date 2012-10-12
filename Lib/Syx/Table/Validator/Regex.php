<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

require_once 'Syx/Table/Validator/Interface.php';

/**
 * Syx_Table_Validator_Regex
 *
 * @category   Syx
 * @package    Syx_Table
 * @subpackage Validator
 */
class Syx_Table_Validator_Regex implements Syx_Table_Validator_Interface
{
	public function __construct($pattern)
	{
		$this->_pattern = (string)$pattern;
		try {
			// "@" cannot fully disable errors reporting when an exception was made in error
			$status = @preg_match($this->_pattern, "Test");
		} catch (Exception $e) {
		}

		if (false === $status) {
			require_once 'Syx/Table/Validator/Exception.php';
			throw new Syx_Table_Validator_Exception("Invalid pattern '$this->_pattern'");
		}
	}

	public function isValid($value)
	{
		return is_null($value) || preg_match($this->_pattern, $value);
	}
}