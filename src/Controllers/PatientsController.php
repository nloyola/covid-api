<?php

namespace App\Controllers;

use App\Utils\LoggerFuncs;
use App\Services\PatientsService;
use App\Factories\LoggerFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class PatientsController extends Controller
{
  use LoggerFuncs;

  public function __construct(LoggerFactory $loggerFactory, PatientsService $patientsService) {
    parent::__construct($loggerFactory);
    $this->logger = $loggerFactory->addConsoleHandler(1)->createInstance("PatientsController");
    $this->patientsService = $patientsService;
  }

  public function getReports(Request $request, Response $response, $args)
  {
    $validation = $this->patientsService->reports($args);
    return $this->jsonResponse($response, $validation, 400);
  }

}
