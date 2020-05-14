<?php

namespace App\Controllers;

use App\Cookies\Cookies;
use App\Factories\LoggerFactory;
use App\Utils\LoggerFuncs;
use App\Services\UserService;
use App\Validation\ValidationError;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use UAParser\Parser;

class UsersController extends Controller {

  use LoggerFuncs;

  public function __construct(LoggerFactory $loggerFactory, UserService $userService) {
    parent::__construct($loggerFactory);
    $this->logger = $loggerFactory->addConsoleHandler(1)->createInstance("UsersController");
    $this->userService = $userService;
  }

  public function getUser(Request $request, Response $response, $args) {
    $validation = $this->userService->user($args["slug"]);
    return $this->jsonResponse($response, $validation, 404);

  }

  public function postForgotPassword(Request $request, Response $response, $args) {
    if (!array_key_exists('email', $request->getParsedBody())) {
      $validation = new ValidationError(['error' => [ 'email' => 'email address not specified'] ]);
      return $this->jsonResponse($response, $validation, 404);
    }

    $email = $request->getParsedBody()['email'];
    $operatingSystem = 'unknown';
    $browserName = 'unknown';
    $userAgentHeader = $request->getHeader('User-Agent');

    if (count($userAgentHeader) > 0) {
      $userAgent = Parser::create()->parse($userAgentHeader[0]);
      $operatingSystem = $userAgent->os->family;
      $browserName = $userAgent->ua->family;
    }

    $actionUrlBase = $this->getBaseUrl($request) . '#!/login/password-reset';

    $result = $this->userService->forgotPassword($email, $operatingSystem, $browserName, $actionUrlBase);
    return $this->jsonResponse($response, $result, 404);
  }

  public function postPasswordReset(Request $request, Response $response, $args) {
    $result = $this->userService->resetPassword($request->getParsedBody());
    if ($result->failed()) {
      return $this->jsonResponse($response, $result, 404);
    }
    $token = $result->value()['token'];
    $user = $result->value()['user'];
    $c = new Cookies($request, $response);
    $response = $c->set("XSRF-TOKEN", $token);
    return $response->withJson($user);
  }

  public function postUpdate(Request $request, Response $response, $args)
  {
    $values = array_merge($request->getParsedBody(), $args);
    $validation = $this->userService->updateUser($values);
    return $this->jsonResponse($response, $validation, 400);
  }
}
