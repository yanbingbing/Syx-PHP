<?php
return array(
	'appKey'           => 'app',
	'defaultAppName'   => 'system',
	'appBasePath'      => ROOT_PATH . '/Apps',
	'appConfigFile'    => 'Config/Config.php',
	'controllerPaths'  => 'Controller',
	'includePaths'     => array('Model', ROOT_PATH . '/Shared'),
	'routes'           => array(
		'normal'=> array(
			'rule'=> '/:app/*'
		)
	),

	'php'              => array(
		'date.timezone' => "UTC"
	),
	'log'              => array(
		'Syx_Log_Writer_FireBug'
	),
	'controllerSuffix' => 'Controller',
	'controllerKey'    => 'c',
	'actionKey'        => 'a',
	'db'               => array(
		'adapter' => 'Mysql',
		'params'  => include ROOT_PATH . '/Config/Database.php',
		'cache'   => array(
			'adapter' => 'Array',
			'options' => array(
				'duration' => 0,
				'cacheDir' => ROOT_PATH . '/Data/Cache/Db',
				'dirDepth' => 0
			)
		)
	),
	'session'          => array(
		'options' => array(
			'save_path' => ROOT_PATH . '/Data/Session'
		)
	)
);