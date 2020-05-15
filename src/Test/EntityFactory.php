<?php

namespace App\Test;

use App\Models\Affected;
use App\Models\Patient;
use App\Models\Examination;
use App\Models\DoseFrequencies;
use App\Models\DoseRoutes;
use App\Models\DoseUnits;
use App\Models\Gender;
use App\Models\MedicalHistoryStatus;
use App\Models\SwabResults;
use App\Models\SymptomResolutions;
use App\Models\SymptomStatus;
use App\Models\TestStatus;
use App\Models\YesNo;
use App\Models\YesNoUnknown;
use App\Utils\LoggerFuncs;
use Faker\Factory as FakerFactory;

class EntityFactory
{
  use LoggerFuncs;

  private $faker;

  public function __construct()
  {
    $this->faker = FakerFactory::create();
  }

  /**
   * @return Patient
   */
  public function patient() {
    $instance = new Patient();

    $instance->record_id          = $this->faker->swiftBicNumber();
    $instance->phn                = $this->faker->creditCardNumber();
    $instance->age                = $this->faker->numberBetween(0, 90);
    $instance->current_status     = $this->faker->randomElement(TestStatus::validValues());
    $instance->testing_date       = $this->faker->dateTimeBetween('-90 days', '-80 days')->format('m-d-Y');
    $instance->positive_test_date = $this->faker->dateTimeBetween('-80 days', '-10 days')->format('m-d-Y');
    $instance->sex                = $this->faker->randomElement(Gender::validValues());
    $instance->bloodtype          = $this->faker->randomElement(['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-']);

    $instance->medical_history = (object) [
      'cld'                        => $this->faker->randomElement(YesNo::validValues()),
      'diabetes'                   => $this->faker->randomElement(YesNo::validValues()),
      'cvd'                        => $this->faker->randomElement(YesNo::validValues()),
      'prior_myocardial_infarctio' => $this->faker->randomElement(YesNo::validValues()),
      'prior_coronary_artery_bypa' => $this->faker->randomElement(YesNo::validValues()),
      'prior_coronary_artery_bypa' => $this->faker->randomElement(YesNo::validValues()),
      'prior_percutaneous_coronar' => $this->faker->randomElement(YesNo::validValues()),
      'renaldis'                   => $this->faker->randomElement(YesNo::validValues()),
      'liverdis'                   => $this->faker->randomElement(YesNo::validValues()),
      'immsupp'                    => $this->faker->randomElement(YesNo::validValues()),
      'hyp'                        => $this->faker->randomElement(YesNo::validValues()),
      'hypertension'               => $this->faker->randomElement(YesNo::validValues()),
      'hiv'                        => $this->faker->randomElement(YesNo::validValues()),
      'cerebrovascular_disease'    => $this->faker->randomElement(YesNo::validValues()),
      'prior_stroke'               => $this->faker->randomElement(YesNo::validValues()),
      'obesity'                    => $this->faker->randomElement(YesNo::validValues()),
      'dyslipidemia'               => $this->faker->randomElement(YesNo::validValues()),
      'pregnant'                   => $this->faker->randomElement(YesNo::validValues()),
      'smoke_curr'                 => $this->faker->randomElement(YesNo::validValues()),
      'smoke_former'               => $this->faker->randomElement(YesNo::validValues()),
      'has_other_disease'          => $this->faker->randomElement(YesNo::validValues()),
      'hba1c'                      => $this->faker->randomElement(YesNo::validValues()),
      'hba1c_result'               => $this->faker->numberBetween(0, 100) / 100.0,
      'date_of_most_recent_hba1c'  => $this->faker->dateTimeBetween('-70 days')->format('m-d-Y'),
    ];

    if ($instance->medical_history->has_other_disease == 'yes') {
      $instance->medical_history->other_disease = $this->faker->words(3, true);
    }

    $instance->relevant_history = [];
    $relevant_history = $this->faker->randomElement(YesNo::validValues());

    if ($relevant_history == 'yes') {
      for ($i = 0; $i < $this->faker->numberBetween(0, 5); $i++) {
        $instance->relevant_history[] = $this->relevantHistory();
      }
    }

    $instance->physical_exam = (object) [
      'general_appearance'         => $this->faker->randomElement(Examination::validValues()),
      'lungs_chest'                => $this->faker->randomElement(Examination::validValues()),
      'skin'                       => $this->faker->randomElement(Examination::validValues()),
      'head_ears_eyes_nose_throat' => $this->faker->randomElement(Examination::validValues()),
      'neck'                       => $this->faker->randomElement(Examination::validValues()),
      'lymph_nodes'                => $this->faker->randomElement(Examination::validValues()),
      'genitourinary'              => $this->faker->randomElement(Examination::validValues()),
      'heart'                      => $this->faker->randomElement(Examination::validValues()),
      'mouth'                      => $this->faker->randomElement(Examination::validValues()),
      'abdomen_gastrointestinal'   => $this->faker->randomElement(Examination::validValues()),
      'extremities'                => $this->faker->randomElement(Examination::validValues()),
      'neurological'               => $this->faker->randomElement(Examination::validValues()),
      'musculoskeletal'            => $this->faker->randomElement(Examination::validValues()),
      'thyroid'                    => $this->faker->randomElement(Examination::validValues()),
      'back_spinal'                => $this->faker->randomElement(Examination::validValues()),
      'external_genitalia'         => $this->faker->randomElement(Examination::validValues()),
      'comments'                   => $this->faker->words(5, true),
      'height'                     => $this->faker->numberBetween(80, 200),
      'weight'                     => $this->faker->numberBetween(60, 200),
      'waist_circumference'        => $this->faker->numberBetween(60, 150),
      'date_of_physical_exam'      => $this->faker->dateTimeBetween('-70 days')->format('m-d-Y'),
    ];

    $instance->concomitant_medications = [];
    $concomitant_medications = $this->faker->numberBetween(0, 5);

    for ($i = 0; $i < $concomitant_medications; $i++) {
      $instance->concomitant_medications[] = (object) [
        'name'            => $this->faker->words(5, true),
        'indication'      => $this->faker->words(5, true),
        'resp_complaint'  => $this->faker->randomElement(YesNo::validValues()),
        'dose_amt'        => $this->faker->numberBetween(20, 100),
        'dose_unit'       => $this->faker->randomElement(DoseUnits::validValues()),
        'dose_route'      => $this->faker->randomElement(DoseRoutes::validValues()),
        'dose_frequency'  => $this->faker->randomElement(DoseFrequencies::validValues()),
        'dose_start_date' => $this->faker->dateTimeBetween('-70 days')->format('m-d-Y'),
        'dose_stop_check' => $this->faker->randomElement(YesNo::validValues()),
        'dose_stop_date'  => $this->faker->dateTimeBetween('-60 days')->format('m-d-Y'),
      ];
    }

    $instance->symptoms = (object) [
      'status'               => $this->faker->randomElement(SymptomStatus::validValues()),
      'resolution'           => $this->faker->randomElement(SymptomResolutions::validValues()),
      'resolution_date'      => $this->faker->dateTimeBetween('-70 days')->format('m-d-Y'),
      'fever'                => $this->faker->randomElement(YesNoUnknown::validValues()),
      'subjective_fever'     => $this->faker->randomElement(YesNoUnknown::validValues()),
      'chills'               => $this->faker->randomElement(YesNoUnknown::validValues()),
      'myalgia'              => $this->faker->randomElement(YesNoUnknown::validValues()),
      'runny_nose'           => $this->faker->randomElement(YesNoUnknown::validValues()),
      'sore_throat'          => $this->faker->randomElement(YesNoUnknown::validValues()),
      'cough'                => $this->faker->randomElement(YesNoUnknown::validValues()),
      'sob'                  => $this->faker->randomElement(YesNoUnknown::validValues()),
      'nauseavomit'          => $this->faker->randomElement(YesNoUnknown::validValues()),
      'headache'             => $this->faker->randomElement(YesNoUnknown::validValues()),
      'abdom'                => $this->faker->randomElement(YesNoUnknown::validValues()),
      'diarrhea'             => $this->faker->randomElement(YesNoUnknown::validValues()),
      'anosmia'              => $this->faker->randomElement(YesNoUnknown::validValues()),
      'pneumonia'            => $this->faker->randomElement(YesNoUnknown::validValues()),
      'acute_resp_distress'  => $this->faker->randomElement(YesNoUnknown::validValues()),
      'abxchest'             => $this->faker->randomElement(YesNoUnknown::validValues()),
      'hosp'                 => $this->faker->randomElement(YesNoUnknown::validValues()),
      'discharged'           => $this->faker->randomElement(YesNoUnknown::validValues()),
      'icu'                  => $this->faker->randomElement(YesNoUnknown::validValues()),
      'mechvent'             => $this->faker->randomElement(YesNoUnknown::validValues()),
      'death'                => $this->faker->randomElement(YesNoUnknown::validValues()),
    ];

    $onset_unknown = $this->faker->randomElement([ '', 'unknown']);
    if ($onset_unknown == '') {
      $instance->symptoms->onset_date = $this->faker->dateTimeBetween('-70 days')->format('m-d-Y');
    }

    if ($instance->symptoms->acute_resp_distress == 'yes') {
      $instance->symptoms->ards_date            = $this->faker->dateTimeBetween('-70 days', '-60 days')->format('m-d-Y');
      $instance->symptoms->ards_resolution_date = $this->faker->dateTimeBetween('-60 days')->format('m-d-Y');
    }

    if ($instance->symptoms->hosp == 'yes') {
      $instance->symptoms->admission_date = $this->faker->dateTimeBetween('-70 days', '-50 days')->format('m-d-Y');

      if ($instance->symptoms->discharged == 'yes') {
        $instance->symptoms->discharged_date = $this->faker->dateTimeBetween('-50 days')->format('m-d-Y');
      }
    }

    if ($instance->symptoms->icu == 'yes') {
      $instance->symptoms->icu_date = $this->faker->dateTimeBetween('-50 days')->format('m-d-Y');
      $instance->symptoms->icu_proned = $this->faker->words(5, true);
      $instance->symptoms->icu_discharged = $this->faker->randomElement(YesNoUnknown::validValues());
      $instance->symptoms->ecmo = $this->faker->randomElement(YesNoUnknown::validValues());

      if ($instance->symptoms->discharged == 'yes') {
        $instance->symptoms->icu_discharged_date = $this->faker->dateTimeBetween('-50 days')->format('m-d-Y');
      }
    }

    if ($instance->symptoms->mechvent == 'yes') {
      $instance->symptoms->mechvent_dur = $this->faker->numberBetween(1, 5);
    }

    if ($instance->symptoms->death == 'yes') {
      $death_date_unknown = $this->faker->randomElement([ '', 'unknown']);
      if ($death_date_unknown == '') {
        $instance->death_date = $this->faker->dateTimeBetween('-40 days')->format('m-d-Y');
      }
    }

    $instance->other_symptoms = [];
    $other_symptoms = $this->faker->numberBetween(0, 3);

    for ($i = 0; $i < $other_symptoms; $i++) {
      $instance->other_symptoms[] = $this->faker->words(5, true);
    }


    $instance->affected_family_members = (object) [
      'mother'        => $this->faker->randomElement(Affected::validValues()),
      'father'        => $this->faker->randomElement(Affected::validValues()),
      'brother'       => $this->faker->randomElement(Affected::validValues()),
      'sister'        => $this->faker->randomElement(Affected::validValues()),
      'spouse'        => $this->faker->randomElement(Affected::validValues()),
      'child'         => $this->faker->randomElement(Affected::validValues()),
      'aunt'          => $this->faker->randomElement(Affected::validValues()),
      'uncle'         => $this->faker->randomElement(Affected::validValues()),
      'cousin'        => $this->faker->randomElement(Affected::validValues()),
      'grandmother'   => $this->faker->randomElement(Affected::validValues()),
      'grandfather'   => $this->faker->randomElement(Affected::validValues()),
      'grandchildren' => $this->faker->randomElement(Affected::validValues()),
    ];

    $instance->nasopharyngleal_swab_samples = [];
    $nasopharyngleal_swab_samples = $this->faker->numberBetween(0, 3);
    for ($i = 0; $i < $nasopharyngleal_swab_samples; $i++) {
      $instance->nasopharyngleal_swab_samples[] = $this->nasopharynglealSwabSample();
    }

    $instance->blood_samples = [];
    $blood_samples = $this->faker->numberBetween(0, 10);
    for ($i = 0; $i < $blood_samples; $i++) {
      $instance->blood_samples[] = (object) [
        'blood_id'         => $this->faker->swiftBicNumber(),
        'blood_date'       => $this->faker->dateTimeBetween('-90 days')->format('m-d-Y'),
        'blood_processing' => $this->faker->dateTimeBetween('-70 days')->format('m-d-Y'),
      ];
    }

    $instance->case_report_form_patient_data_complete = $this->faker->randomElement(Patient::$reportComplete);

    return $instance;
  }

  public function relevantHistory() {
    return (object) [
      'history'                  => $this->faker->words(5, true),
      'condition'                => $this->faker->words(5, true),
      'condition_onset_date'     => $this->faker->dateTimeBetween('-70 days')->format('m-d-Y'),
      'present_at_time_of_covid' => $this->faker->randomElement(YesNo::validValues()),
      'condition_worsened'       => $this->faker->randomElement(YesNo::validValues()),
      'status'                   => $this->faker->randomElement(MedicalHistoryStatus::validValues()),
    ];
  }

  public function nasopharynglealSwabSample() {
    return (object) [
      'swab_id'         => $this->faker->swiftBicNumber(),
      'swab_date'       => $this->faker->dateTimeBetween('-40 days')->format('m-d-Y'),
      'processing_date' => $this->faker->dateTimeBetween('-30 days')->format('m-d-Y'),
      'result'          => $this->faker->randomElement(SwabResults::validValues()),
    ];
  }

}
