<?php

namespace App\Controllers;

use App\Utils\LoggerFuncs;
use App\Factory\LoggerFactory;
use App\Cookies\Cookies;
use App\Services\UserService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

// Use the "Insomian" app to try out REST API
class AuthController extends Controller {

  use LoggerFuncs;

  public function __construct(LoggerFactory $loggerFactory, UserService $userService) {
    parent::__construct($loggerFactory);
    $this->userService = $userService;
  }

  public function postSignup(Request $request, Response $response, $args) {
    $validation = $this->userService->addUser($request->getParams());
    return $this->jsonResponse($response, $validation, 400);
  }

  public function postLogin(Request $request, Response $response, $args) {
    $validation = $this->userService->userLogin($request->getParams());
    if ($validation->failed()) {
      $errors = join(",", $validation->errors());
      if (strpos($errors, "unauthorized") >= 0) {
        return $response->withStatus(401);
      }
      return $response->withStatus(400)->withJson($validation->errors());
    }

    ["token" => $token, "user" => $user] = $validation->value();
    $c = new Cookies($request, $response);
    $response = $c->set("XSRF-TOKEN", $token);

    return $response->withJson([
      "slug"  => $user->slug,
      "name"  => $user->name,
      "email" => $user->email
    ]);
  }

  public function postAuth(Request $request, Response $response, $args) {
    $userId = $request->getAttribute("userId");
    $validation = $this->userService->userById($userId);
    if ($validation->failed()) {
      return $response->withStatus(401)->withJson([ 'message' => 'invalid token' ]);
    }

    $user = $validation->value();
    return $response->withJson([
      "slug"  => $user->slug,
      "name" => $user->name,
      "email" => $user->email
    ]);
  }

  public function postLogout(Request $request, Response $response, $args) {
    $this->userService->userLogout();
    $c = new Cookies($request, $response);
    $c->set("XSRF-TOKEN", "");
    return $response->withJson([ "message" => "ok" ]);
  }

}
