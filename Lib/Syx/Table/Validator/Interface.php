<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Table_Validator_Interface
 *
 * @category Syx
 * @package  Syx_Table
 * @subpackage Validator
 */
Interface Syx_Table_Validator_Interface
{
	/**
	 * Check if valid
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function isValid($value);
}