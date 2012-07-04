<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Kakalong CHINA (http://yanbingbing.com)
 */

require_once 'Syx/Cache/Abstract.php';

/**
 * Memcached-cache Adapter
 *
 * @category   Syx
 * @package    Syx_Cache
 */
class Syx_Cache_Memcached extends Syx_Cache_Abstract
{

	/**
	 * @var Memcache
	 */
	protected $_memcache;

	protected $_options = array(
		'lifetime'    => false,
		'servers'     => false,
		'compression' => false,
	);

	protected $_config = array(
		'lifetime'    => 3600,
		'servers'     => array(
			'127.0.0.1:11211'
		),
		'compression' => false
	);

	const DEFAULT_HOST = '127.0.0.1';
	const DEFAULT_PORT = 11211;
	const DEFAULT_PERSISTENT = true;
	const DEFAULT_WEIGHT = 1;
	const DEFAULT_TIMEOUT = 1;

	protected function _init()
	{
		if (!extension_loaded('memcache')) {
			require_once 'Syx/Cache/Exception.php';
			throw new Syx_Cache_Exception('The memcache extension must be loaded before use!');
		}

		if (!is_array($this->_config['servers'])) {
			$this->_config['servers'] = array($this->_config['servers']);
		}
		$this->_memcache = new Memcache();
		foreach ($this->_config['servers'] as $server) {
			if (is_string($server)) {
				$server = explode(':', $server);
				$host = $server[0];
				$port = 11211;
				if (!empty($server[1])) {
					$port = (int)$server[1];
				}
				$persistent = self::DEFAULT_PERSISTENT;
				$weight = self::DEFAULT_WEIGHT;
				$timeout = self::DEFAULT_TIMEOUT;
			} elseif (is_array($server)) {
				$host = array_key_exists('host', $server) ? $server['host'] : self::DEFAULT_HOST;
				$port = array_key_exists('port', $server) ? $server['port'] : self::DEFAULT_PORT;
				$persistent = array_key_exists('persistent', $server) ? $server['persistent'] : self::DEFAULT_PERSISTENT;
				$weight = array_key_exists('weight', $server) ? $server['weight'] : self::DEFAULT_WEIGHT;
				$timeout = array_key_exists('timeout', $server) ? $server['timeout'] : self::DEFAULT_TIMEOUT;
			} else {
				continue;
			}
			$this->_memcache->addServer($host, $port, $persistent, $weight, $timeout);
		}
	}

	/**
	 * @see Syx_Cache_Abstract
	 */
	public function clean($mode = Syx_Cache::CLEANING_ALL)
	{
		if ($mode == Syx_Cache::CLEANING_ALL) {
			return $this->_memcache->flush();
		}
		return false;
	}

	/**
	 * @see Syx_Cache_Abstract
	 */
	public function write($guid, $data, $specificLifetime = null)
	{
		if ($this->_config['compression']) {
			$flag = MEMCACHE_COMPRESSED;
		} else {
			$flag = 0;
		}
		$lifetime = $specificLifetime ? $specificLifetime : $this->_config['lifetime'];
		if (false === $this->_memcache->add($guid, $data, $flag, $lifetime)) {
			return $this->_memcache->replace($guid, $data, $flag, $lifetime);
		}
		return true;
	}

	/**
	 * @see Syx_Cache_Abstract
	 */
	public function read($guid)
	{
		return $this->_memcache->get($guid);
	}

	/**
	 * @see Syx_Cache_Abstract
	 */
	public function delete($guid)
	{
		return $this->_memcache->delete($guid);
	}

	/**
	 * @see Syx_Cache_Abstract
	 */
	public function touch($guid, $lifetime)
	{
		if ($this->_config['compression']) {
			$flag = MEMCACHE_COMPRESSED;
		} else {
			$flag = 0;
		}
		$data = $this->_memcache->get($guid);
		if ($data) {
			return $this->_memcache->replace($guid, $data, $flag, $lifetime);
		}
		return false;
	}
}