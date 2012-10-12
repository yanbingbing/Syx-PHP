<?php
/**
 * Syx Framework
 *
 * @copyright Copyright (c) 2009-2012 Binbing CHINA (http://yanbingbing.com)
 */

require_once 'Syx/Config.php';

require_once 'Helper/Yaml/sfYamlParser.class.php';

require_once 'Helper/Yaml/sfYamlInline.class.php';

/**
 * Syx_Config_Yaml
 *
 * @category  Syx
 * @package   Syx_Config
 */
class Syx_Config_Yaml extends Syx_Config
{
	/**
	 * yaml file parse engine
	 *
	 * @var sfYamlParser
	 */
	protected static $_parser = null;

	public function __construct($file = null)
	{
		$this->merge(self::parse($file));
	}

	/**
	 * parse data to array
	 *
	 * @param $filename
	 *
	 * @throws Syx_Config_Exception
	 * @return array
	 */
	public static function parse($filename)
	{
		try {
			$yamlString = file_get_contents($filename);
		} catch (Exception $e) {
			require_once 'Syx/Config/Exception.php';
			throw new Syx_Config_Exception("Cannot load Yaml file '$filename'");
		}

		if (self::$_parser == null) {
			self::$_parser = new sfYamlParser();
		}

		try {
			$yamlArray = self::$_parser->parse($yamlString);
		} catch (Exception $e) {
			require_once 'Syx/Config/Exception.php';
			throw new Syx_Config_Exception($e->getMessage());
		}

		return $yamlArray;
	}
}