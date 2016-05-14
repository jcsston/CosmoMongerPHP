<?php
// Extra libraries used
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/protected/library/Date-1.4.7/');
require_once('Date.php');

// change the following paths if necessary
//$yii=dirname(__FILE__).'/../yii-1.0.7.r1212/framework/yii.php';
$yii=dirname(__FILE__).'/../yii-1.1.3.r2247/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
error_reporting(E_ALL | E_STRICT);

require_once($yii);
Yii::createWebApplication($config)->run();
