<?php

use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as Capsule;
use Respect\Validation\Validator as v;
use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';

//v::with('App\\Validation\\Rules\\');

$containerBuilder = new ContainerBuilder();

// Set up settings
$containerBuilder->addDefinitions(__DIR__ . '/container.php');

// Build PHP-DI Container instance
$container = $containerBuilder->build();

$app = $container->get(App::class);

(require __DIR__ . '/middleware.php')($app);
(require __DIR__ . '/routes.php')($app);

$container->get(Capsule::class);

return $app;
