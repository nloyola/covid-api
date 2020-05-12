<?php

namespace App\Controllers;

use App\LoggerFuncs;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Selective\Config\Configuration;

class RedcapController extends Controller
{
  use LoggerFuncs;

  public function __construct(
    LoggerFactory $loggerFactory,
    RedcapService $redcapService) {
    parent::__construct($loggerFactory);
    $this->redcapService = $redcapService;
  }

  public function getReports(Request $request, Response $response, $args)
  {
    $validation = $this->clinicService->clinicSummaries($args);
    return $this->jsonResponse($response, $validation, 404);
  }

}
