<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Log
 *
 * @category  Syx
 * @package   Syx_Log
 */
class Syx_Log
{
	const ALERT = 1;
	const LOG = 2;
	const DEBUG = 3;
	const ERROR = 4;
	const WARN = 5;
	const NOTICE = 6;

	/**
	 * @var array of log priorities
	 */
	protected $_type = array(
		self::WARN   => 'WARN',
		self::LOG    => 'LOG',
		self::ERROR  => 'ERROR',
		self::ALERT  => 'ALERT',
		self::DEBUG  => 'DEBUG',
		self::NOTICE => 'NOTICE'
	);

	/**
	 * @var array of Syx_Log_Writer
	 */
	protected $_writers = array();

	protected static $_instance = null;

	/**
	 * Class constructor.  Create a new Log
	 */
	protected function __construct()
	{
	}

	/**
	 * get a singleton log object
	 *
	 * @return Syx_Log
	 */
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * quick add log message to cache
	 */
	public static function log($msg, $priority)
	{
		self::getInstance()->append($msg, $priority);
	}

	/**
	 * Add a writer.  A writer is responsible for taking a log
	 * message and writing it out to storage.
	 *
	 * @param  Syx_Log_Writer $writer
	 *
	 * @return void
	 */
	public function addWriter(Syx_Log_Writer $writer)
	{
		$this->_writers[] = $writer;
		$str = str_pad(' Syx START ', 68, '*', STR_PAD_BOTH);
		$writer->append($this->_event($str, self::LOG));
		$writer->append($this->_event('REQUEST_URI:' . $_SERVER['PHP_SELF'], self::LOG));
	}

	/**
	 * append log to writers
	 *
	 * @param string $message
	 * @param string $priority
	 *
	 * @throws Syx_Log_Exception
	 */
	public function append($message, $priority)
	{
		if (!isset($this->_type[$priority])) {
			require_once 'Syx/Log/Exception.php';
			throw new Syx_Log_Exception('Bad log priority');
		}

		$event = $this->_event($message, $priority);

		// send to each writer
		foreach ($this->_writers as $writer) {
			$writer->append($event);
		}
	}

	/**
	 * format a event
	 *
	 * @param $message
	 * @param $priority
	 *
	 * @return array
	 */
	protected function _event($message, $priority)
	{
		$priorityName = $this->_type[$priority];
		return array(time(), $message, $priority, $priorityName);
	}

	/**
	 * Class destructor.  Shutdown log writers
	 *
	 * @return void
	 */
	public function __destruct()
	{
		foreach ($this->_writers as $writer) {
			try {
				$writer->write();
			} catch (Exception $e) {
			}
		}
	}
}