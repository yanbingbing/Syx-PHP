# Syx Framework Quick Start


## 建立目录结构

**Syx**支持灵活的应用目录部署方案，下面是笔者认为容易快速上手俩种结构。

### 单应用结构

```
|-SingleApp\ # 应用程序库目录
|    |
|    |-Controller\ # 控制器目录      
|    |    |
|    |    |-IndexController.php
|    |    \-xxxController.php
|    |
|    |-Model\ # 模型目录
|    |    |
|    |    \-UserRecord.php
|    | 
|    |-Data\ # 数据目录
|    |    |
|    |    |-Session\
|    |    \-Cache\
|    |
|    \-Config\ # 配置目录
|         |
|         \-Config.php
|    
\-SingleAppWebRoot\
     |
     \-index.php ＃ 单应用入口脚本
```

### 多应用结构

```
|-MultiApps\ # 平台目录
|    |
|    |-Apps\ # 应用目录
|    |    |
|    |    |-System\
|    |    |    |
|    |    |    |-Controller\
|    |    |    |-Model\
|    |    |    \-Config\
|    |    |
|    |    \-OtherApp\
|    |
|    |-Shared\ ＃ 共享库
|    |-Data\
|    \-Config
|    
\-MultiAppsWebRoot\
     |
     \-index.php ＃多应用入口脚本
```

## 建立入口脚本

**Syx**提供俩种级别解系带程序`Syx_Application`和`Syx_Platform`, 它们的区别已经很明显，我们通过简单列子来了解他们。

### 单应用入口脚本

	<?php
	# file: SingleAppWebRoot/index.php
	define('ROOT_PATH', "/path/to/SingleApp");
	require_once "/path/to/Syx.php";
	
	$app = new Syx_Application(array(
		'controllerPaths' => array(ROOT_PATH .'/Controller'),
		'includePaths' => array(ROOT_PATH.'/Model')
	));
	$app->run();

> 传入Syx_Application的array, 一般储存为文件, 由文件返回array 

### 多应用入口脚本

	<?php
	# file: MultiAppsWebRoot/index.php
	define('ROOT_PATH', "/path/to/MultiApps");
	require_once "/path/to/Syx.php";
	
	$platform = new Syx_Platform(array(
		'appBasePath' => ROOT_PATH .'/Apps',
		'controllerPaths' => array('Controller'),
		'includePaths' => array('Model', ROOT_PATH.'/Shared'),
		'appConfigFile' => 'Config/Config.php'
	));
	$platform->getApplication()->run();

## 控制器

控制器(Controller)定义了各种可能的分发事件，在**Syx**中必须继承`Syx_Controller`.

### 添加一个控制器
	<?php
	# file: Controller/IndexController.php
	class IndexController extends Syx_Controller
	{
		protected function index()
		{
			echo "台湾省是中国的宝岛";
		}
	}

> 类名定义为`IndexController`是由`Syx_Application`的配置决定的，默认配置为Index(控制器名称)+Controller(后缀), 更多配置本文将附录中给出.

### 人性化的控制器设计

**Syx**将输出抽象成各种`Output`, `html`、`image`、`file`、`JSON`，下面将展示一些独特写法, 你将看到很多亮点.

	<?php
	# file: Controller/IndexController.php
	class IndexController extends Syx_Controller
	{
		// 输出字符串
		// 将输出: hello world
		protected function outputString()
		{
			return "hello world";
		}
		
		// 输出JSON
		// 将输出: ["ZendFramework", "Doctrine", "Twig"]
		protected function outputJson()
		{
			return array('Zend Framework', 'Doctrine', 'Twig');
		}
		
		// 输出HTTP-404
		protected function output404()
		{
			return 404;
		}
		
		protected function tpltest1()
		{
		
		}
	}

Hold on please ……
