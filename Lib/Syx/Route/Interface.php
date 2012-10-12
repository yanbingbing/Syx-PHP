<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

/**
 * Syx_Route_Interface
 *
 * @category  Syx
 * @package   Syx_Route
 */
interface Syx_Route_Interface
{
	const URI_DELI = '/';

	const REGEX_DELI = '#';

	const URL_KEY = ':';

	/**
	 * match request(Syx_Request_Abstract) to this route
	 *
	 * @param Syx_Request_Abstract $request
	 *
	 * @return array|bool
	 */
	public function match(Syx_Request_Abstract $request);

	/**
	 * constructor
	 *
	 * @param array $config
	 */
	public function __construct(array $config);
}