<?php

namespace App\Models;

class GenderReport
{
  /**
   * @var TestCounts
   */
  public $unknown;

  /**
   * @var TestCounts
   */
  public $other;

  /**
   * @var TestCounts
   */
  public $female;

  /**
   * @var TestCounts
   */
  public $male;

  public function __construct() {
    $this->unknown = new TestCounts();
    $this->other = new TestCounts();
    $this->female = new TestCounts();
    $this->male = new TestCounts();
  }

  public function updateWithPatient($patient)
  {
    if ($patient->isCovid19Positive()) {
      if ($patient->hasGenderMale()) {
        $this->male->positiveCount++;
      }

      if ($patient->hasGenderFemale()) {
        $this->female->positiveCount++;
      }

      if ($patient->hasGenderOther()) {
        $this->other->positiveCount++;
      }

      if ($patient->hasGenderUnknown()) {
        $this->unknown->positiveCount++;
      }
    } else if ($patient->isCovid19Negative()) {
      if ($patient->hasGenderMale()) {
        $this->male->negativeCount++;
      }

      if ($patient->hasGenderFemale()) {
        $this->female->negativeCount++;
      }

      if ($patient->hasGenderOther()) {
        $this->other->negativeCount++;
      }

      if ($patient->hasGenderUnknown()) {
        $this->unknown->negativeCount++;
      }
    }
  }
}
