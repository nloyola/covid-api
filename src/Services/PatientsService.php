<?php

namespace App\Services;

use App\Utils\LoggerFuncs;
use App\Factories\LoggerFactory;
use App\Test\FakeReporter;
use App\Validation\ValidationResult;

class PatientsService
{

  use LoggerFuncs;
  use HasUpdateValidator;

  public function __construct(LoggerFactory $loggerFactory) {
    $this->logger = $loggerFactory->addConsoleHandler(1)->createInstance("PatientsSerivce");
  }

  /**
   * @return App\Validation\Validation;
   */
  public function reports($options = []) {
    $reporter = new FakeReporter();
    $report = $reporter->report();

    $this->varLog('====> report', $report->testResults);

    return new ValidationResult($report);
  }

}
