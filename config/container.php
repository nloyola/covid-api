<?php

//declare(strict_types=1);

use App\Auth\Auth;
use App\Factories\LoggerFactory;
use App\Middleware\TokenValidation;
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Container\ContainerInterface;
use Selective\Config\Configuration;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Slim\Views\TwigRuntimeLoader;

return [
  // Application settings
  Configuration::class => function () {
    return new Configuration(require __DIR__ . '/settings.php');
  },

  App::class => function (ContainerInterface $container) {
    AppFactory::setContainer($container);
    $app = AppFactory::create();

    $config = $container->get(Configuration::class);
    $routeCacheFile = $config->findString('router.cache_file');
    if ($routeCacheFile) {
      $app->getRouteCollector()->setCacheFile($routeCacheFile);
    }

    return $app;
  },

  // Twig templates
  Twig::class => function (ContainerInterface $container) {
    $config = $container->get(Configuration::class);
    $twigSettings = $config->getArray('twig');

    $twig = Twig::create($twigSettings['path'], [
      'cache' => $twigSettings['cache_enabled'] ? $twigSettings['cache_path'] : false,
    ]);

    $loader = $twig->getLoader();
    if ($loader instanceof FilesystemLoader) {
      $loader->addPath($config->getString('public'), 'public');
    }

    // Add the Twig extension only when running the app from the command line / cron job,
    // but not when phpunit tests are running.
    if ((PHP_SAPI === 'cli' || PHP_SAPI === 'cgi-fcgi') && !defined('PHPUNIT_TEST_SUITE')) {
      $app = $container->get(App::class);
      $routeParser = $app->getRouteCollector()->getRouteParser();
      $uri = (new UriFactory())->createUri('http://localhost');

      $runtimeLoader = new TwigRuntimeLoader($routeParser, $uri);
      $twig->addRuntimeLoader($runtimeLoader);
      $twig->addExtension(new TwigExtension());
    }

    return $twig;
  },

  LoggerFactory::class => function (ContainerInterface $container) {
    $settings = $container->get(Configuration::class)->getArray('logger');
    $loggerFactory = new LoggerFactory($settings);

    if ((PHP_SAPI === 'cli' || PHP_SAPI === 'cgi-fcgi') && !defined('PHPUNIT_TEST_SUITE')) {
      $loggerFactory->addConsoleHandler($settings['level']);
    }
    return $loggerFactory;
  },

  Capsule::class => function (ContainerInterface $container) {
    $config = $container->get(Configuration::class);
    $settings = $config->getArray('db');

    $capsule = new Capsule();
    $capsule->addConnection($settings);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    // set timezone for timestamps etc
    date_default_timezone_set('UTC');

    // for more info see https://gist.github.com/wingsline/4441139
    $connection = $capsule->getConnection('default');
    if ($connection instanceof \Illuminate\Database\SQLiteConnection) {
      $connection->getPdo()->sqliteCreateFunction('REGEXP', function ($pattern, $value) {
        mb_regex_encoding('UTF-8');
        return (false !== mb_ereg($pattern, $value)) ? 1 : 0;
      });
      $connection->getPdo()->sqliteCreateFunction('radians', 'deg2rad', 1);
      $connection->getPdo()->sqliteCreateFunction('cos', 'cos', 1);
      $connection->getPdo()->sqliteCreateFunction('acos', 'acos', 1);
      $connection->getPdo()->sqliteCreateFunction('sin', 'sin', 1);
    }

    return $capsule;
  },

  TokenValidation::class  => function (ContainerInterface $container) {
    $loggerFactory = $container->get(LoggerFactory::class);
    $auth = $container->get(Auth::class);
    $tokenValidation = new TokenValidation($loggerFactory, $auth);
    return $tokenValidation;
  },
];
