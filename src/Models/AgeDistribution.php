<?php

namespace App\Models;

class AgeDistribution
{
  public $categories = [];

  public function __construct()
  {
    foreach (AgeRanges::validValues() as $range) {
      $this->categories[$range['label']] = new TestCounts();
    }
  }

  /**
   * @param Patient $patient
   */
  public function updateWithPatient($patient)
  {
    $range = AgeRanges::getRangeForAge($patient->age);

    if ($patient->isCovid19Positive()) {
      $this->categories[$range['label']]->positiveCount++;
    } else if ($patient->isCovid19Negative()) {
      $this->categories[$range['label']]->negativeCount++;
    }
  }
}
