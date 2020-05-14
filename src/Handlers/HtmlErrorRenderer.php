<?php

namespace App\Handlers;

use App\Factories\LoggerFactory;
use App\Utils\ExceptionDetail;
use Slim\Interfaces\ErrorRendererInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Throwable;

final class HtmlErrorRenderer implements ErrorRendererInterface
{
  private $logger;

  public function __construct(LoggerFactory $loggerFactory)
  {
    $this->logger = $loggerFactory
      ->addFileHandler('html_error.log')
      ->createInstance('html_error_renderer');
  }

  public function __invoke(Throwable $exception, bool $displayErrorDetails): string
  {
    $detailedErrorMessage = ExceptionDetail::getExceptionText($exception);
    $this->logger->error($detailedErrorMessage);
    if ($displayErrorDetails) {
      return $detailedErrorMessage;
    }

    // Detect error type
    if ($exception instanceof HttpNotFoundException) {
      $errorMessage = '404 Not Found';
    } elseif ($exception instanceof HttpMethodNotAllowedException) {
      $errorMessage = '405 Method Not Allowed';
    } else {
      $errorMessage = '500 Internal Server Error';
    }

    return $errorMessage;
  }
}
