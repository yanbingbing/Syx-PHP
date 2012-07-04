<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Auth_Adapter_Interface
 *
 * @category   Syx
 * @package    Syx_Auth
 * @subpackage Adapter
 */
interface Syx_Auth_Adapter_Interface
{
	/**
	 * Performs an authentication attempt
	 *
	 * @return Syx_Auth_Result
	 */
	public function authenticate();
}