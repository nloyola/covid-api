<?php

namespace App\Models;

class PositiveGenderByAge
{
  public $categories = [];

  public function __construct()
  {
    foreach (AgeRanges::validValues() as $range) {
      $this->categories[$range['label']] = [];
      foreach (Gender::legalValues() as $gender) {
        $this->categories[$range['label']][$gender['label']] = 0;
      }
    }
  }

  /**
   * @param Patient $patient
   */
  public function updateWithPatient($patient)
  {
    $range = AgeRanges::getRangeForAge($patient->age);

    if ($patient->isCovid19Positive()) {
      foreach (Gender::legalValues() as $gender) {
        if ($patient->hasGender($gender)) {
          $this->categories[$range['label']][$gender['label']]++;
        }
      }
    }
  }
}
