<?php

namespace App\Test;

use App\Models\Report;

class FakeReporter
{
  private $entityFactory;

  public function __construct()
  {
    $this->entityFactory = new EntityFactory();
  }

  /**
   *
   */
  public function report() {
    $patients = [];
    $patientCount = 200;
    $report = new Report($patientCount);

    for ($i = 0; $i < $patientCount; ++$i) {
      $patient = $this->entityFactory->patient();
      $patients[] = $patient;
      $report->updateWithPatient($patient);
    }

    return $report;
  }

}
