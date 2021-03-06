<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

require_once 'Syx/Log/Writer.php';

require_once 'Helper/FirePHP/FirePHP.class.php';

/**
 * Syx_Log_Writer_FireBug
 *
 * @category   Syx
 * @package    Syx_Log
 * @subpackage Writer
 */
class Syx_Log_Writer_FireBug extends Syx_Log_Writer
{
	/**
	 * @var array of log priorities
	 */
	protected $_type = array(
		LOG_WARNING => FirePHP::WARN,
		LOG_NOTICE  => FirePHP::INFO,
		LOG_INFO    => FirePHP::INFO,
		LOG_DEBUG   => FirePHP::LOG,
		LOG_ERR     => FirePHP::ERROR,
		LOG_ALERT   => FirePHP::ERROR,
		LOG_EMERG   => FirePHP::ERROR
	);
	/**
	 * firephp instance
	 *
	 * @var FirePHP
	 */
	protected $_fb = null;

	public function __construct()
	{
		$this->_fb = FirePHP::getInstance(true);
	}

	/**
	 *  format a message and add to the log.
	 *
	 * @param  array  $event  log data event
	 *
	 * @return void
	 */
	public function append($event)
	{
		list($timestamp, $message, $priority, $priorityName) = $event;
		$priority = array_key_exists($priority, $this->_type)
			? $this->_type[$priority] : FirePHP::LOG;
		$this->_fb->fb($message, $priority);
	}
}