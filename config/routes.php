<?php

use App\Controllers\AuthController;
use App\Controllers\UsersController;
use App\Controllers\PatientsController;
use App\Middleware\JwtMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function(App $app) {
  $app->get('/', \App\Actions\HomeAction::class);

  $app->group('/api', function (RouteCollectorProxy $group) {

    $group->group('/auth', function (RouteCollectorProxy $group) {

      $group->post('/auth', AuthController::class . ':postAuth');
    });

    $group->group('/users', function (RouteCollectorProxy $group) {

      $group->get('/{slug}', UsersController::class . ':getUser');

      $group->post('/update/{slug}', UsersController::class . ':postUpdate');
    });

  })->add(JwtMiddleware::class);

  $app->group('/api', function (RouteCollectorProxy $group) {

    $group->group('/auth', function (RouteCollectorProxy $group) {

      $group->post('/signup', AuthController::class . ':postSignup');

      $group->post('/login', AuthController::class . ':postLogin');
    });

    $group->group('/users', function (RouteCollectorProxy $group) {

      $group->post('/forgotpass', UsersController::class . ':postForgotPassword');

      $group->post('/passreset', UsersController::class . ':postPasswordReset');

    });

    $group->group('/patients', function (RouteCollectorProxy $group) {

      $group->get('/reports', PatientsController::class . ':getReports');
    });

  });
};
