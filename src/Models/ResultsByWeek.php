<?php

namespace App\Models;

class ResultsByWeek
{
  public $weeks = [];

  /**
   * @param Patient $patient
   */
  public function updateWithPatient($patient)
  {
    if (isset($patient->testing_date)) {
      $date = \DateTime::createFromFormat('m-d-Y', $patient->testing_date);
      $week = 'Week ' . $date->format('W');

      if (!isset($this->weeks[$week])) {
        $this->weeks[$week] = new TestCounts();
      }

      if ($patient->isCovid19Positive()) {
        $this->weeks[$week]->positiveCount++;
      } else if ($patient->isCovid19Negative()) {
        $this->weeks[$week]->negativeCount++;
      }
    }
  }
}
