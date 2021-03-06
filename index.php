<?php

require './vendor/autoload.php';
date_default_timezone_set('America/La_Paz');
define("PROJECTPATH", __DIR__);
define("PROJECTNAME", substr(PROJECTPATH, strrpos(PROJECTPATH, DIRECTORY_SEPARATOR) + 1));
define("IP", $_SERVER['SERVER_NAME']);
define("PRIVATEKEY", "ISEEDEADPEOPLE");

$whitelist = array(
  '127.0.0.1',
  '::1'
);

$configFile = '';

if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
  $configFile = '/src/Database/config.db';
} else {
  $configFile = '/src/Database/configs.db';
}

$dbconfig = parse_ini_file(PROJECTPATH . $configFile);

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host'] = $dbconfig['host'];
$config['db']['username'] = $dbconfig['user'];
$config['db']['password'] = $dbconfig['password'];
$config['db']['database'] = $dbconfig['database'];
$config['db']['driver'] = 'mysql';
$config['db']['charset'] = 'utf8';
$config['db']['collation'] = 'utf8_general_ci';

$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();
/* $container['errorHandler'] = function ($c) {
return function ($request, $response, $exception) use ($c) {
$data;
$data['code'] = 500;
$data['devmsg'] = $exception->getMessage();
$data['usrmsg'] = "error";
$data['content'] = null;
return $c['response']->withJson($data);
};
}; */
// LOGGER
/* $container['logger'] = function($c) {
$logger = new \Monolog\Logger('my_logger');
$file_handler = new \Monolog\Handler\StreamHandler('./logs/app.log');
$logger->pushHandler($file_handler);
return $logger;
}; */
// VIEWS AND PDF
//$container['view'] = new \Slim\Views\PhpRenderer(PROJECTPATH.'/src/templates/');
//$container['mpdf'] = new \Mpdf\Mpdf(['tempDir'   => PROJECTPATH.'/files/temp']);

$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container->get('settings')['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

include_once PROJECTPATH . '/src/Routes/Routes.php';
$app->run();
