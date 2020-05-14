<?php

namespace App\Controllers;

use App\Utils\LoggerFuncs;
use App\Factories\LoggerFactory;
use App\Services\UserService;
use App\Validation\ValidationError;
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
    $validation = $this->userService->addUser($request->getParsedBody());
    return $this->jsonResponse($response, $validation, 400);
  }

  public function postLogin(Request $request, Response $response, $args) {
    $validation = $this->userService->userLogin($request->getParsedBody());
    if ($validation->failed()) {
      $errors = join(",", $validation->errors());
      if (strpos($errors, "unauthorized") >= 0) {
        return $response->withStatus(401);
      }
      return $this->jsonResponse($response, $validation, 400);
    }

    $payload = json_encode($validation->value());
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
  }

  public function postAuth(Request $request, Response $response, $args) {
    $userId = $request->getAttribute("uid");
    $validation = $this->userService->userById($userId);
    if ($validation->failed()) {
      $validation = new ValidationError([ 'message' => 'invalid token' ]);
      return $this->jsonResponse($response, $validation, 400);
    }

    $user = $validation->value();
    $payload = json_encode([
      "slug"  => $user->slug,
      "name" => $user->name,
      "email" => $user->email
    ]);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  // Logout not supported on server side when using JWT
  //
  // client should delete token from local storage

  // public function postLogout(Request $request, Response $response, $args) {
  //   $this->userService->userLogout();
  //   $payload = json_encode([ "message" => "ok" ]);
  //   $response->getBody()->write($payload);
  //   return $response->withHeader('Content-Type', 'application/json');
  // }

}
