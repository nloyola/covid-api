<?php declare(strict_types=1);

use App\Models\Patient;
use App\Test\EntityFactory;
use App\Utils\LoggerFuncs;
use PHPUnit\Framework\TestCase;

final class PatientTest extends TestCase
{

  use LoggerFuncs;
  private $factory;

  /**
   * @dataProvider covid19StatusProvider
   */
  public function testCovid19Status($covid19Status): void
  {
    $patient = $this->factory->patient();
    $patient->current_status = $covid19Status;

    switch ($covid19Status) {
      case 'positive':
        $this->assertTrue($patient->isCovid19Positive());
        break;
      case 'negative':
        $this->assertTrue($patient->isCovid19Negative());
        break;
    }

  }

  /**
   * @dataProvider genderProvider
   */
  public function testGender($gender): void
  {
    $patient = $this->factory->patient();
    $patient->sex = $gender;

    switch ($gender) {
      case 'unknown':
        $this->assertTrue($patient->hasGenderUnknown());
        break;
      case 'other':
        $this->assertTrue($patient->hasGenderOther());
        break;
      case 'female':
        $this->assertTrue($patient->hasGenderFemale());
        break;
      case 'male':
        $this->assertTrue($patient->hasGenderMale());
        break;
    }

  }

  /**
   * @dataProvider hasComorbiditiesProvider
   */
  public function testHasComorbidities($comorbidity, $value): void
  {
    $patient = $this->factory->patient();
    $patient = $this->patientHasExistingCondition($patient, false);
    $patient->medical_history->{$comorbidity} = $value;

    $this->assertEquals($value, $patient->hasComorbidity());
  }

  /**
   * @dataProvider physicalExamProvider
   */
  public function testAbmormalPhysicalExam($item, $value): void
  {
    $patient = $this->factory->patient();
    $patient = $this->patientPhysicalExamNormal($patient);
    $patient->physical_exam->{$item} = $value;

    $this->assertEquals($value, $patient->abnormalPhisicalExam());
  }

  /**
   * @dataProvider symptomStatusProvider
   */
  public function testSymptomStatus($value): void
  {
    $patient = $this->factory->patient();
    $patient->symptoms->status = $value;

    $this->assertEquals($value == 'symptomatic', $patient->symptomatic());
  }

  /**
   * @dataProvider swabResultsProvider
   */
  public function testSwabResult($value): void
  {
    $patient = $this->factory->patient();
    $swabSample = $this->factory->nasopharynglealSwabSample();
    $swabSample->result = $value;
    $patient->nasopharyngleal_swab_samples = [ $swabSample ];

    $this->assertEquals($value == 'positive', $patient->testedPositive());
  }

  /**
   * @return Patient
   */
  private function patientHasExistingCondition($patient, bool $value) {
    $result = clone $patient;
    $result->medical_history->cld                        = $value;
    $result->medical_history->diabetes                   = $value;
    $result->medical_history->cvd                        = $value;
    $result->medical_history->prior_myocardial_infarctio = $value;
    $result->medical_history->prior_coronary_artery_bypa = $value;
    $result->medical_history->prior_coronary_artery_bypa = $value;
    $result->medical_history->prior_percutaneous_coronar = $value;
    $result->medical_history->renaldis                   = $value;
    $result->medical_history->liverdis                   = $value;
    $result->medical_history->immsupp                    = $value;
    $result->medical_history->hyp                        = $value;
    $result->medical_history->hypertension               = $value;
    $result->medical_history->hiv                        = $value;
    $result->medical_history->cerebrovascular_disease    = $value;
    $result->medical_history->prior_stroke               = $value;
    $result->medical_history->obesity                    = $value;
    $result->medical_history->dyslipidemia               = $value;
    $result->medical_history->pregnant                   = $value;
    $result->medical_history->smoke_curr                 = $value;
    $result->medical_history->smoke_former               = $value;
    $result->medical_history->has_other_disease          = $value;
    $result->medical_history->hba1c                      = $value;

    if ($value) {
      $result->relevant_history = [ $this->factory->relevantHistory() ];
    } else {
      $result->relevant_history = [];
    }

    return $result;
  }

  /**
   * @return Patient
   */
  private function patientPhysicalExamNormal($patient) {
    $result = clone $patient;
    $items = [
      'general_appearance',
      'lungs_chest',
      'skin',
      'head_ears_eyes_nose_throat',
      'neck',
      'lymph_nodes',
      'genitourinary',
      'heart',
      'mouth',
      'abdomen_gastrointestinal',
      'extremities',
      'neurological',
      'musculoskeletal',
      'thyroid',
      'back_spinal',
      'external_genitalia'
    ];

    foreach ($items as $item) {
      $result->physical_exam->{$item}  = 'normal';
    }

    return $result;
  }

  protected function setUp(): void {
    $this->factory = new EntityFactory();
  }

  public function covid19StatusProvider() {
    $result = array_map(
      function ($status) {
        return [ $status ];
      },
      Patient::$covid19Status
    );
    return $result;
  }

  public function genderProvider() {
    $result = array_map(
      function ($gender) {
        return [ $gender ];
      },
      Patient::$genders
    );
    return $result;
  }

  public function hasComorbiditiesProvider()
  {
    $comorbidities = [
      'cld',
      'diabetes',
      'cvd',
      'prior_myocardial_infarctio',
      'prior_coronary_artery_bypa',
      'prior_coronary_artery_bypa',
      'prior_percutaneous_coronar',
      'renaldis',
      'liverdis',
      'immsupp',
      'hyp',
      'hypertension',
      'hiv',
      'cerebrovascular_disease',
      'prior_stroke',
      'obesity',
      'dyslipidemia',
      'pregnant',
      'smoke_curr',
      'smoke_former',
      'has_other_disease',
      'hba1c',
    ];
    $result = [];
    foreach ($comorbidities as $comorbidity) {
      $result[$comorbidity . '_true'] = [ $comorbidity, true ];
      $result[$comorbidity . '_false'] = [ $comorbidity, false ];
    }
    return $result;
  }

  public function physicalExamProvider()
  {
    $symptoms = [
      'general_appearance',
      'lungs_chest',
      'skin',
      'head_ears_eyes_nose_throat',
      'neck',
      'lymph_nodes',
      'genitourinary',
      'heart',
      'mouth',
      'abdomen_gastrointestinal',
      'extremities',
      'neurological',
      'musculoskeletal',
      'thyroid',
      'back_spinal',
      'external_genitalia'
    ];
    $result = [];
    foreach ($symptoms as $symptom) {
      $result[$symptom . '_true'] = [$symptom, true];
      $result[$symptom . '_false'] = [$symptom, false];
    }
    return $result;
  }

  public function symptomStatusProvider() {
    return [
      'symptomatic'  => [ 'symptomatic' ],
      'asymptomatic' => [ 'asymptomatic' ],
      'unknown'      => [ 'unknow' ],
    ];
  }

  public function swabREsultsProvider() {
    $result = [];
    foreach (Patient::$swabResults as $swabResult) {
      $result[$swabResult] = [ $swabResult ];
    }
    return $result;
  }
}
