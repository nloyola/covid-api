<?php

namespace App\Models;

class ComorbidityReport
{
  /**
   * @var TestCounts
   */
  public $cld;

  /**
   * @var TestCounts
   */
  public $diabetes;

  /**
   * @var TestCounts
   */
  public $cvd;

  /**
   * @var TestCounts
   */
  public $prior_myocardial_infarctio;

  /**
   * @var TestCounts
   */
  public $prior_coronary_artery_bypa;

  /**
   * @var TestCounts
   */
  public $prior_percutaneous_coronar;

  /**
   * @var TestCounts
   */
  public $renaldis;

  /**
   * @var TestCounts
   */
  public $liverdis;

  /**
   * @var TestCounts
   */
  public $immsupp;

  /**
   * @var TestCounts
   */
  public $hyp;

  /**
   * @var TestCounts
   */
  public $hypertension;

  /**
   * @var TestCounts
   */
  public $hiv;

  /**
   * @var TestCounts
   */
  public $cerebrovascular_disease;

  /**
   * @var TestCounts
   */
  public $prior_stroke;

  /**
   * @var TestCounts
   */
  public $obesity;

  /**
   * @var TestCounts
   */
  public $dyslipidemia;

  /**
   * @var TestCounts
   */
  public $pregnant;

  /**
   * @var TestCounts
   */
  public $smoke_curr;

  /**
   * @var TestCounts
   */
  public $smoke_former;

  /**
   * @var TestCounts
   */
  public $has_other_disease;

  /**
   * @var TestCounts
   */
  public $hba1c;

  public function __construct()
  {
    $this->cld                        = new TestCounts();
    $this->diabetes                   = new TestCounts();
    $this->cvd                        = new TestCounts();
    $this->prior_myocardial_infarctio = new TestCounts();
    $this->prior_coronary_artery_bypa = new TestCounts();
    $this->prior_percutaneous_coronar = new TestCounts();
    $this->renaldis                   = new TestCounts();
    $this->liverdis                   = new TestCounts();
    $this->immsupp                    = new TestCounts();
    $this->hyp                        = new TestCounts();
    $this->hypertension               = new TestCounts();
    $this->hiv                        = new TestCounts();
    $this->cerebrovascular_disease    = new TestCounts();
    $this->prior_stroke               = new TestCounts();
    $this->obesity                    = new TestCounts();
    $this->dyslipidemia               = new TestCounts();
    $this->pregnant                   = new TestCounts();
    $this->smoke_curr                 = new TestCounts();
    $this->smoke_former               = new TestCounts();
    $this->has_other_disease          = new TestCounts();
    $this->hba1c                      = new TestCounts();
  }

  /**
   * @param Patient $patient
   */
  public function updateWithPatient($patient)
  {
    foreach (Patient::$comorbidities as $key) {
      if ($patient->medical_history->{$key} == YesNo::Yes) {
        if ($patient->isCovid19Positive()) {
          $this->{$key}->positiveCount++;
        } else if ($patient->isCovid19Negative()) {
          $this->{$key}->negativeCount++;
        }
      }
    }
  }
}
