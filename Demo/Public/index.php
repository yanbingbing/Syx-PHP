<?php
/**
 * bootstrap
 *
 * @copyright Copyright (c) 2011-2012 kakalong (http://yanbingbing.com)
 */

define('ROOT_PATH', rtrim(str_replace('\\', '/', dirname(dirname(__FILE__))), '/'));
define('LIB_PATH', rtrim(str_replace('\\', '/', dirname(ROOT_PATH)), '/') . '/Lib');

require_once LIB_PATH . '/Syx.php';

$platform = new Syx_Platform(ROOT_PATH .'/Config/Config.php');
$platform->getApplication()->run();