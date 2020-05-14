<?php

namespace App\Controllers;

use App\Factories\LoggerFactory;
use App\Services\PaginatedResult;
use App\Validation\Validation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class Controller {

  protected $logger;

  // constructor receives container instance
  public function __construct(LoggerFactory $loggerFactory) {
    $this->logger = $loggerFactory->addFileHandler("controllers.log")->createInstance('controllers');
  }

  protected function jsonResponse(ResponseInterface $response,
                                  Validation $validation,
                                  int $errorStatus) {
    if ($validation->failed()) {
      $payload = json_encode([ 'errors' => $validation->errors() ]);
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json')->withStatus($errorStatus);
    }

    $payload = json_encode($validation->value());
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  protected function pdfResponse(ResponseInterface $response,
                                 Validation $validation,
                                 int $errorStatus) {
    if ($validation->failed()) {
      return $response->withStatus($errorStatus)->withJson([ 'errors' => $validation->errors() ]);
    }
    $response = $response->withHeader('Content-type', 'application/pdf');
    $body = $response->getBody();
    $body->write($validation->value());
    return $response;
  }

  protected function jsonPaginatedResponse(ResponseInterface $response,
                                           Validation $validation,
                                           int $erroStatus) {
    if ($validation->failed()) {
      $payload = json_encode([ 'errors' => $validation->errors() ]);
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json')->withStatus($errorStatus);
    }
    $value = $validation->value();
    if (!$value instanceof PaginatedResult) {
      throw new \Error('expected PaginatedResult in validation');
    }

    $payload = json_encode([
      'data' => $value->data(),
      'meta' => [
        'pagination' => $value->pagination()
      ]
    ]);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  protected function getBaseUrl(Request $request): string
  {
    return $request->getUri()->getScheme() . '://'
      . $request->getUri()->getHost() . ':'
      . $request->getUri()->getPort()
      . '/';
  }

}
