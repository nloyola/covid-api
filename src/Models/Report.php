<?php

namespace App\Models;

class Report
{
  /**
   * @var integer
   */
  public $patientCount;

  /**
   * @var TestResults
   */
  public $testResults;

  public function __construct(int $patientCount) {
    $this->patientCount = $patientCount;
    $this->testResults = new TestResults();
  }

  /**
   * @param Patient $patient
   */
  public function updateWithPatient($patient)
  {
    $this->testResults->updateWithPatient($patient);
  }
}
