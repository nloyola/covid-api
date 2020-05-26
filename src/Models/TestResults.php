<?php

namespace App\Models;

class TestResults
{
  /**
   * @var AgeDistribution
   */
  public $ageDistribution;

  /**
   * @var GenderReport
   */
  public $gender;

  /**
   * @var ComorbidityReport
   */
  public $comorbidity;

  /**
   * @var TestCounts
   */
  public $symptomatic;

  /**
   * @var TestCounts
   */
  public $asymptomatic;

  /**
   * @var AsymptomaticComorbidity
   */
  public $asymptomaticComorbidity;

  /**
   * @var PositiveGenderByAge
   */
  public $positiveGenderByAge;

  /**
   * @var ResultsByWeek
   */
  public $resultsByWeek;

  public function __construct() {
    $this->ageDistribution = new AgeDistribution();
    $this->gender = new GenderReport();
    $this->comorbidity = new ComorbidityReport();
    $this->symptomatic = new TestCounts();
    $this->asymptomatic = new TestCounts();

    $this->asymptomaticComorbidity = new AsymptomaticComorbidity();
    $this->positiveGenderByAge = new PositiveGenderByAge();
    $this->resultsByWeek = new ResultsByWeek();
  }

  /**
   * @param Patient $patient
   */
  public function updateWithPatient($patient)
  {
    $this->ageDistribution->updateWithPatient($patient);
    $this->gender->updateWithPatient($patient);
    $this->comorbidity->updateWithPatient($patient);
    $this->asymptomaticComorbidity->updateWithPatient($patient);
    $this->positiveGenderByAge->updateWithPatient($patient);
    $this->resultsByWeek->updateWithPatient($patient);

    if ($patient->isCovid19Positive()) {
      if ($patient->symptomatic()) {
        $this->symptomatic->positiveCount++;
      } else {
        $this->asymptomatic->positiveCount++;
      }
    } else if ($patient->isCovid19Negative()) {
      if ($patient->symptomatic()) {
        $this->symptomatic->negativeCount++;
      } else {
        $this->asymptomatic->negativeCount++;
      }
    }
  }

}
