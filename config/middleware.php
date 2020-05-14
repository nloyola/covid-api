<?php

use App\Handlers\HtmlErrorRenderer;
use App\Handlers\JsonErrorRenderer;
use Selective\Config\Configuration;
use Slim\App;

return function (App $app) {
  $container = $app->getContainer();
  $settings = $container->get(Configuration::class)->getArray('error_handler_middleware');

  $app->addRoutingMiddleware();
  $app->addBodyParsingMiddleware();

  if ((PHP_SAPI === 'cli' || PHP_SAPI === 'cgi-fcgi') && !defined('PHPUNIT_TEST_SUITE')) {
    $logErrors = true;
    $logErrorDetails = true;
  } else {
    $logErrors = (bool) $settings['logErrors'];
    $logErrorDetails = (bool) $settings['logErrorDetails'];
  }

  $errorMiddleware = $app->addErrorMiddleware(
    (bool) $settings['displayErrorDetails'],
    $logErrors,
    $logErrorDetails
  );

  $errorHandler = $errorMiddleware->getDefaultErrorHandler();
  $errorHandler->registerErrorRenderer('text/html', HtmlErrorRenderer::class);
  $errorHandler->registerErrorRenderer('application/json', JsonErrorRenderer::class);

  $app->add(new \Slim\Middleware\Session([
    'name'        => 'covid_collaboration',
    'autorefresh' => true,
    'lifetime'    => '1 hour'
  ]));
};
