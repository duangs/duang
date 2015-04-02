<?php
define("DS", '/');
define("APP_PATH", dirname(__FILE__) . DS . '..' . DS . 'application' . DS);

include dirname(__FILE__) . DS . '..' . DS . 'vendor/autoload.php';

$app = new Yaf_Application(APP_PATH . "conf/application.ini");
$app->bootstrap()->run();
