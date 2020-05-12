<?php

namespace App\Actions;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

/**
 * Action.
 */
final class HomeAction
{
  private $twig;

  public function __construct(Twig $twig)
  {
    $this->twig = $twig;
  }

  public function __invoke(Request $request, Response $response): Response
  {
    return $this->twig->render($response, 'index.html');
  }
}
