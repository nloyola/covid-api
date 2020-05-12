<?php

namespace App\Middleware;

use App\Auth\Auth;
use App\Factories\LoggerFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class TokenValidation {

  public function __construct(LoggerFactory $loggerFactory, Auth $auth) {
    $this->logger = $loggerFactory->addConsoleHandler(null)->createInstance('token');
    $this->auth = $auth;
  }

  public function __invoke(ServerRequestInterface $request, RequestHandler $handler) {
    if ($request->hasHeader('X-XSRF-TOKEN')) {
      $xsrfTokenHeader = $request->getHeader('X-XSRF-TOKEN')[0];
      if ($this->auth->validateToken($xsrfTokenHeader)) {
        $userId = $this->auth->userId();
        $request = $request->withAttribute('userId', $userId);
        $response = $handler->handle($request);
        return $response;
      }
    }

    $response = new Response();
    $response->getBody()->write((string)json_encode([ 'message' => 'invalid token' ]));
    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(401);
  }
}
