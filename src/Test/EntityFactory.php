<?php

namespace App\Test;

use App\Models\Patient;
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
    $instance->current_status     = $this->faker->randomElement(['negative', 'positive']);
    $instance->testing_date       = $this->faker->dateTimeBetween('-90 days')->format('m-d-Y');
    $instance->positive_test_date = $this->faker->dateTimeBetween('-70 days')->format('m-d-Y');
    $instance->sex                = $this->faker->randomElement(['male', 'female', 'other', 'unknown']);
    $instance->bloodtype          = $this->faker->randomElement(['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-']);

    $instance->medical_history = (object) [
      'cld'                        => $this->faker->randomElement(Patient::$yesNoUnknown),
      'diabetes'                   => $this->faker->randomElement(Patient::$yesNoUnknown),
      'cvd'                        => $this->faker->randomElement(Patient::$yesNoUnknown),
      'prior_myocardial_infarctio' => $this->faker->randomElement(Patient::$yesNoUnknown),
      'prior_coronary_artery_bypa' => $this->faker->randomElement(Patient::$yesNoUnknown),
      'prior_coronary_artery_bypa' => $this->faker->randomElement(Patient::$yesNoUnknown),
      'prior_percutaneous_coronar' => $this->faker->randomElement(Patient::$yesNoUnknown),
      'renaldis'                   => $this->faker->randomElement(Patient::$yesNoUnknown),
      'liverdis'                   => $this->faker->randomElement(Patient::$yesNoUnknown),
      'immsupp'                    => $this->faker->randomElement(Patient::$yesNoUnknown),
      'hyp'                        => $this->faker->randomElement(Patient::$yesNoUnknown),
      'hypertension'               => $this->faker->randomElement(Patient::$yesNoUnknown),
      'hiv'                        => $this->faker->randomElement(Patient::$yesNoUnknown),
      'cerebrovascular_disease'    => $this->faker->randomElement(Patient::$yesNoUnknown),
      'prior_stroke'               => $this->faker->randomElement(Patient::$yesNoUnknown),
      'obesity'                    => $this->faker->randomElement(Patient::$yesNoUnknown),
      'dyslipidemia'               => $this->faker->randomElement(Patient::$yesNoUnknown),
      'pregnant'                   => $this->faker->randomElement(Patient::$yesNoUnknown),
      'smoke_curr'                 => $this->faker->randomElement(Patient::$yesNoUnknown),
      'smoke_former'               => $this->faker->randomElement(Patient::$yesNoUnknown),
      'has_other_disease'          => $this->faker->randomElement(Patient::$yesNoUnknown),
      'hba1c'                      => $this->faker->randomElement(Patient::$yesNoUnknown),
      'hba1c_result'               => $this->faker->numberBetween(0, 100) / 100.0,
      'date_of_most_recent_hba1c'  => $this->faker->dateTimeBetween('-70 days')->format('m-d-Y'),
    ];

    if ($instance->medical_history->has_other_disease == 'yes') {
      $instance->medical_history->other_disease = $this->faker->words(3, true);
    }

    $instance->relevant_history = [];
    $relevant_history = $this->faker->randomElement(Patient::$yesNo);

    if ($relevant_history == 'yes') {
      for ($i = 0; $i < $this->faker->numberBetween(0, 5); $i++) {
        $instance->relevant_history[] = $this->relevantHistory();
      }
    }

    $instance->physical_exam = (object) [
      'general_appearance'         => $this->faker->randomElement(Patient::$examination),
      'lungs_chest'                => $this->faker->randomElement(Patient::$examination),
      'skin'                       => $this->faker->randomElement(Patient::$examination),
      'head_ears_eyes_nose_throat' => $this->faker->randomElement(Patient::$examination),
      'neck'                       => $this->faker->randomElement(Patient::$examination),
      'lymph_nodes'                => $this->faker->randomElement(Patient::$examination),
      'genitourinary'              => $this->faker->randomElement(Patient::$examination),
      'heart'                      => $this->faker->randomElement(Patient::$examination),
      'mouth'                      => $this->faker->randomElement(Patient::$examination),
      'abdomen_gastrointestinal'   => $this->faker->randomElement(Patient::$examination),
      'extremities'                => $this->faker->randomElement(Patient::$examination),
      'neurological'               => $this->faker->randomElement(Patient::$examination),
      'musculoskeletal'            => $this->faker->randomElement(Patient::$examination),
      'thyroid'                    => $this->faker->randomElement(Patient::$examination),
      'back_spinal'                => $this->faker->randomElement(Patient::$examination),
      'external_genitalia'         => $this->faker->randomElement(Patient::$examination),
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
        'resp_complaint'  => $this->faker->randomElement(Patient::$yesNo),
        'dose_amt'        => $this->faker->numberBetween(20, 100),
        'dose_unit'       => $this->faker->randomElement(Patient::$doseUnit),
        'dose_route'      => $this->faker->randomElement(Patient::$doseRoute),
        'dose_frequency'  => $this->faker->randomElement(Patient::$doseFrequency),
        'dose_start_date' => $this->faker->dateTimeBetween('-70 days')->format('m-d-Y'),
        'dose_stop_check' => $this->faker->randomElement(Patient::$yesNo),
        'dose_stop_date'  => $this->faker->dateTimeBetween('-60 days')->format('m-d-Y'),
      ];
    }

    $instance->symptoms = (object) [
      'status'               => $this->faker->randomElement(Patient::$symptomStatus),
      'resolution'           => $this->faker->randomElement(Patient::$symptomResolution),
      'resolution_date'      => $this->faker->dateTimeBetween('-70 days')->format('m-d-Y'),
      'fever'                => $this->faker->randomElement(Patient::$yesNoUnknown),
      'subjective_fever'     => $this->faker->randomElement(Patient::$yesNoUnknown),
      'chills'               => $this->faker->randomElement(Patient::$yesNoUnknown),
      'myalgia'              => $this->faker->randomElement(Patient::$yesNoUnknown),
      'runny_nose'           => $this->faker->randomElement(Patient::$yesNoUnknown),
      'sore_throat'          => $this->faker->randomElement(Patient::$yesNoUnknown),
      'cough'                => $this->faker->randomElement(Patient::$yesNoUnknown),
      'sob'                  => $this->faker->randomElement(Patient::$yesNoUnknown),
      'nauseavomit'          => $this->faker->randomElement(Patient::$yesNoUnknown),
      'headache'             => $this->faker->randomElement(Patient::$yesNoUnknown),
      'abdom'                => $this->faker->randomElement(Patient::$yesNoUnknown),
      'diarrhea'             => $this->faker->randomElement(Patient::$yesNoUnknown),
      'anosmia'              => $this->faker->randomElement(Patient::$yesNoUnknown),
      'pneumonia'            => $this->faker->randomElement(Patient::$yesNoUnknown),
      'acute_resp_distress'  => $this->faker->randomElement(Patient::$yesNoUnknown),
      'abxchest'             => $this->faker->randomElement(Patient::$yesNoUnknown),
      'hosp'                 => $this->faker->randomElement(Patient::$yesNoUnknown),
      'discharged'           => $this->faker->randomElement(Patient::$yesNoUnknown),
      'icu'                  => $this->faker->randomElement(Patient::$yesNoUnknown),
      'mechvent'             => $this->faker->randomElement(Patient::$yesNoUnknown),
      'death'                => $this->faker->randomElement(Patient::$yesNoUnknown),
    ];

    $onset_unknown = $this->faker->randomElement([ '', 'unknown']);
    if ($onset_unknown == '') {
      $instance->symptoms->onset_date = $this->faker->dateTimeBetween('-70 days')->format('m-d-Y');
    }

    if ($instance->symptoms->acute_resp_distress == 'yes') {
      $instance->symptoms->ards_date            = $this->faker->dateTimeBetween('-70 days')->format('m-d-Y');
      $instance->symptoms->ards_resolution_date = $this->faker->dateTimeBetween('-50 days')->format('m-d-Y');
    }

    if ($instance->symptoms->hosp == 'yes') {
      $instance->symptoms->admission_date = $this->faker->dateTimeBetween('-70 days')->format('m-d-Y');

      if ($instance->symptoms->discharged == 'yes') {
        $instance->symptoms->discharged_date = $this->faker->dateTimeBetween('-50 days')->format('m-d-Y');
      }
    }

    if ($instance->symptoms->icu == 'yes') {
      $instance->symptoms->icu_date = $this->faker->dateTimeBetween('-50 days')->format('m-d-Y');
      $instance->symptoms->icu_proned = $this->faker->words(5, true);
      $instance->symptoms->icu_discharged = $this->faker->randomElement(Patient::$yesNoUnknown);
      $instance->symptoms->ecmo = $this->faker->randomElement(Patient::$yesNoUnknown);

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
      'mother'        => $this->faker->randomElement(Patient::$affected),
      'father'        => $this->faker->randomElement(Patient::$affected),
      'brother'       => $this->faker->randomElement(Patient::$affected),
      'sister'        => $this->faker->randomElement(Patient::$affected),
      'spouse'        => $this->faker->randomElement(Patient::$affected),
      'child'         => $this->faker->randomElement(Patient::$affected),
      'aunt'          => $this->faker->randomElement(Patient::$affected),
      'uncle'         => $this->faker->randomElement(Patient::$affected),
      'cousin'        => $this->faker->randomElement(Patient::$affected),
      'grandmother'   => $this->faker->randomElement(Patient::$affected),
      'grandfather'   => $this->faker->randomElement(Patient::$affected),
      'grandchildren' => $this->faker->randomElement(Patient::$affected),
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
      'present_at_time_of_covid' => $this->faker->randomElement(Patient::$yesNo),
      'condition_worsened'       => $this->faker->randomElement(Patient::$yesNo),
      'status'                   => $this->faker->randomElement(Patient::$medicalHistoryStatus),
    ];
  }

  public function nasopharynglealSwabSample() {
    return (object) [
      'swab_id'         => $this->faker->swiftBicNumber(),
      'swab_date'       => $this->faker->dateTimeBetween('-40 days')->format('m-d-Y'),
      'processing_date' => $this->faker->dateTimeBetween('-30 days')->format('m-d-Y'),
      'result'          => $this->faker->randomElement(Patient::$swabResults),
    ];
  }

}
