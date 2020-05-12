<?php

namespace App\Controllers;

use App\Factory\LoggerFactory;
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
      return $response->withStatus($errorStatus)->withJson([ 'errors' => $validation->errors() ]);
    }
    return $response->withJson($validation->value());
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
      return $response->withStatus($errorStatus)->withJson([ 'errors' => $validation->errors() ]);
    }
    $value = $validation->value();
    if (!$value instanceof PaginatedResult) {
      throw new \Error('expected PaginatedResult in validation');
    }
    return $response->withJson([
      'data' => $value->data(),
      'meta' => [
        'pagination' => $value->pagination()
      ]
    ]);
  }

  protected function getBaseUrl(Request $request): string
  {
    return $request->getUri()->getScheme() . '://'
      . $request->getUri()->getHost() . ':'
      . $request->getUri()->getPort()
      . '/';
  }

}
