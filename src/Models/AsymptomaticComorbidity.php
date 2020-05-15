<?php

namespace App\Models;

class AsymptomaticComorbidity
{
  public $comorbidityCount;
  public $noComorbidityCount;

  public function __construct() {
    $this->comorbidityCount = 0;
    $this->noComorbidityCount = 0;
  }

  /**
   * @param Patient $patient
   */
  public function updateWithPatient($patient)
  {
    if ($patient->isCovid19Positive() && !$patient->symptomatic()) {
      if ($patient->hasComorbidity()) {
        $this->comorbidityCount++;
      } else {
        $this->noComorbidityCount++;
      }
    }
  }
}
