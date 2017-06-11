<?php
session_start();
require ('vendor/autoload.php');
$routes = require('naon/config/routes.php');

// Initilize a loader
$loader = new Nette\Loaders\RobotLoader;
$loader->addDirectory('Modules');
$loader->addDirectory('Themes');
$loader->addDirectory('Naon');

$loader->setTempDirectory('Naon/temp');
$loader->register();

// Handling the configuration files
$map = require('Naon/config.map.php');

foreach ($map as $key => $config) {
    $handler = new raczsimon\nfw\Config\Handler();
    $handler->set($config);
    $handler->parse();
    $GLOBALS[$key] = ($handler->get());
}

// Starting a new app
$app = new raczsimon\nfw\Nfw();
$app->setRoutes($routes);

// Database configuration
if (isset($GLOBALS['main']->database['driver'])) {
    $database = new Naon\Config\Database;
    $GLOBALS['em'] = $database->handle();
}

try {
    $app->startSession();
} catch (Exception $e) {
    $controller = new Modules\Error\Controllers\Bootstrap();
    $controller->init($e);
}