<?php

namespace App\Test;

use App\Utils\LoggerFuncs;
use App\Models\Patient;
use Faker\Factory as FakerFactory;

class EntityFactory
{
  use LoggerFuncs;

  private static $yesNo        = [ 'yes', 'no'];
  private static $yesNoUnknown = [ 'yes', 'no', 'unknown'];
  private static $medicalHistoryStatus = ['resolved', 'ongoing_with_treatment', 'ongoing_without_treatment'];
  private static $examination = [
    'normal',
    'abnormal_not_clinically_significant',
    'abnormal_clinically_significant', 'not_assessed'
  ];

  private static $doseUnit = [
    'application',
    'cap',
    'mg',
    'min',
    'mL',
    'puff',
    'softgel',
    'spray',
    'tab',
    'unit',
    'vial',
  ];

  private static $doseRoute = [
    'GT',
    'NG',
    'TO',
    'IM',
    'IV',
    'NAS',
    'PO',
    'IN',
    'SQ',
    'RE',
    'IH',
  ];

  protected static $doseFrequency = [
    'QD',
    'BID',
    'TID',
    'QID',
    'PRN',
    'QMon/Wed/Fri',
    'QMonth',
    'Once',
  ];

  protected static $symptomStatus = [
    'symptomatic',
    'asymptomatic',
    'unknown',
  ];

  protected static $symptomResolution = [
    'symptom_resolved_unknown_date',
    'still_symptomatic',
    'unknown_symptom_status',
    'symptoms_resolved_with_date',
  ];

  protected static $affected = [
    'covid_19_positive',
    'covid_19_negative',
    'not_tested',
    'unknown',
    'not_applicable',
    ];

  protected static $swabResult = [
    'positive',
    'negative',
    'pending',
    'not_done',
    'indeterminate',
  ];

  protected static $reportComplete = [
    'incomplete',
    'unverified',
    'complete',
  ];

  /**
   * @return Patient
   */
  public function patient() {
    $faker = FakerFactory::create();
    $instance = new Patient();

    $instance->record_id          = $faker->swiftBicNumber();
    $instance->phn                = $faker->creditCardNumber();
    $instance->current_status     = $faker->randomElement(['negative', 'positive']);
    $instance->testing_date       = $faker->dateTimeBetween('-90 days')->format('m-d-Y');
    $instance->positive_test_date = $faker->dateTimeBetween('-70 days')->format('m-d-Y');
    $instance->sex                = $faker->randomElement(['male', 'female', 'other', 'unknown']);
    $instance->bloodtype          = $faker->randomElement(['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-']);


    $instance->medical_history = (object) [
      'cld'                        => $faker->randomElement(self::$yesNoUnknown),
      'diabetes'                   => $faker->randomElement(self::$yesNoUnknown),
      'cvd'                        => $faker->randomElement(self::$yesNoUnknown),
      'prior_myocardial_infarctio' => $faker->randomElement(self::$yesNoUnknown),
      'prior_coronary_artery_bypa' => $faker->randomElement(self::$yesNoUnknown),
      'prior_coronary_artery_bypa' => $faker->randomElement(self::$yesNoUnknown),
      'prior_percutaneous_coronar' => $faker->randomElement(self::$yesNoUnknown),
      'renaldis'                   => $faker->randomElement(self::$yesNoUnknown),
      'liverdis'                   => $faker->randomElement(self::$yesNoUnknown),
      'immsupp'                    => $faker->randomElement(self::$yesNoUnknown),
      'hyp'                        => $faker->randomElement(self::$yesNoUnknown),
      'hypertension'               => $faker->randomElement(self::$yesNoUnknown),
      'hiv'                        => $faker->randomElement(self::$yesNoUnknown),
      'cerebrovascular_disease'    => $faker->randomElement(self::$yesNoUnknown),
      'prior_stroke'               => $faker->randomElement(self::$yesNoUnknown),
      'obesity'                    => $faker->randomElement(self::$yesNoUnknown),
      'dyslipidemia'               => $faker->randomElement(self::$yesNoUnknown),
      'pregnant'                   => $faker->randomElement(self::$yesNoUnknown),
      'smoke_curr'                 => $faker->randomElement(self::$yesNoUnknown),
      'smoke_former'               => $faker->randomElement(self::$yesNoUnknown),
      'has_other_disease'          => $faker->randomElement(self::$yesNoUnknown),
      'hba1c'                      => $faker->randomElement(self::$yesNoUnknown),
      'hba1c_result'               => $faker->numberBetween(0, 100) / 100.0,
      'date_of_most_recent_hba1c'  => $faker->dateTimeBetween('-70 days')->format('m-d-Y'),
    ];

    if ($instance->medical_history->has_other_disease == 'yes') {
      $instance->medical_history->other_disease = $faker->words(3, true);
    }

    $instance->relevant_history = [];
    $relevant_history = $faker->randomElement(self::$yesNo);

    if ($relevant_history == 'yes') {
      for ($i = 0; $i < $faker->numberBetween(0, 5); $i++) {
        $instance->relevant_history[] = (object) [
          'history'                  => $faker->words(5, true),
          'condition'                => $faker->words(5, true),
          'condition_onset_date'     => $faker->dateTimeBetween('-70 days')->format('m-d-Y'),
          'present_at_time_of_covid' => $faker->randomElement(self::$yesNo),
          'condition_worsened'       => $faker->randomElement(self::$yesNo),
          'status'                   => $faker->randomElement(self::$medicalHistoryStatus),
        ];
      }
    }

    $instance->physical_exam = (object) [
      'general_appearance'         => $faker->randomElement(self::$examination),
      'lungs_chest'                => $faker->randomElement(self::$examination),
      'skin'                       => $faker->randomElement(self::$examination),
      'head_ears_eyes_nose_throat' => $faker->randomElement(self::$examination),
      'neck'                       => $faker->randomElement(self::$examination),
      'lymph_nodes'                => $faker->randomElement(self::$examination),
      'genitourinary'              => $faker->randomElement(self::$examination),
      'heart'                      => $faker->randomElement(self::$examination),
      'mouth'                      => $faker->randomElement(self::$examination),
      'abdomen_gastrointestinal'   => $faker->randomElement(self::$examination),
      'extremities'                => $faker->randomElement(self::$examination),
      'neurological'               => $faker->randomElement(self::$examination),
      'musculoskeletal'            => $faker->randomElement(self::$examination),
      'thyroid'                    => $faker->randomElement(self::$examination),
      'back_spinal'                => $faker->randomElement(self::$examination),
      'external_genitalia'         => $faker->randomElement(self::$examination),
      'comments'                   => $faker->words(5, true),
      'height'                     => $faker->numberBetween(80, 200),
      'weight'                     => $faker->numberBetween(60, 200),
      'waist_circumference'        => $faker->numberBetween(60, 150),
      'date_of_physical_exam'      => $faker->dateTimeBetween('-70 days')->format('m-d-Y'),
    ];

    $instance->concomitant_medications = [];
    $concomitant_medications = $faker->numberBetween(0, 5);

    for ($i = 0; $i < $concomitant_medications; $i++) {
      $instance->concomitant_medications[] = (object) [
        'name'            => $faker->words(5, true),
        'indication'      => $faker->words(5, true),
        'resp_complaint'  => $faker->randomElement(self::$yesNo),
        'dose_amt'        => $faker->numberBetween(20, 100),
        'dose_unit'       => $faker->randomElement(self::$doseUnit),
        'dose_route'      => $faker->randomElement(self::$doseRoute),
        'dose_frequency'  => $faker->randomElement(self::$doseFrequency),
        'dose_start_date' => $faker->dateTimeBetween('-70 days')->format('m-d-Y'),
        'dose_stop_check' => $faker->randomElement(self::$yesNo),
        'dose_stop_date'  => $faker->dateTimeBetween('-60 days')->format('m-d-Y'),
      ];
    }

    $instance->symptoms = (object) [
      'status'               => $faker->randomElement(self::$symptomStatus),
      'resolution'           => $faker->randomElement(self::$symptomResolution),
      'resolution_date'      => $faker->dateTimeBetween('-70 days')->format('m-d-Y'),
      'fever'                => $faker->randomElement(self::$yesNoUnknown),
      'subjective_fever'     => $faker->randomElement(self::$yesNoUnknown),
      'chills'               => $faker->randomElement(self::$yesNoUnknown),
      'myalgia'              => $faker->randomElement(self::$yesNoUnknown),
      'runny_nose'           => $faker->randomElement(self::$yesNoUnknown),
      'sore_throat'          => $faker->randomElement(self::$yesNoUnknown),
      'cough'                => $faker->randomElement(self::$yesNoUnknown),
      'sob'                  => $faker->randomElement(self::$yesNoUnknown),
      'nauseavomit'          => $faker->randomElement(self::$yesNoUnknown),
      'headache'             => $faker->randomElement(self::$yesNoUnknown),
      'abdom'                => $faker->randomElement(self::$yesNoUnknown),
      'diarrhea'             => $faker->randomElement(self::$yesNoUnknown),
      'anosmia'              => $faker->randomElement(self::$yesNoUnknown),
      'pneumonia'            => $faker->randomElement(self::$yesNoUnknown),
      'acute_resp_distress'  => $faker->randomElement(self::$yesNoUnknown),
      'abxchest'             => $faker->randomElement(self::$yesNoUnknown),
      'hosp'                 => $faker->randomElement(self::$yesNoUnknown),
      'discharged'           => $faker->randomElement(self::$yesNoUnknown),
      'icu'                  => $faker->randomElement(self::$yesNoUnknown),
      'mechvent'             => $faker->randomElement(self::$yesNoUnknown),
      'death'                => $faker->randomElement(self::$yesNoUnknown),
    ];

    $onset_unknown = $faker->randomElement([ '', 'unknown']);
    if ($onset_unknown == '') {
      $instance->symptoms->onset_date = $faker->dateTimeBetween('-70 days')->format('m-d-Y');
    }

    if ($instance->symptoms->acute_resp_distress == 'yes') {
      $instance->symptoms->ards_date            = $faker->dateTimeBetween('-70 days')->format('m-d-Y');
      $instance->symptoms->ards_resolution_date = $faker->dateTimeBetween('-50 days')->format('m-d-Y');
    }

    if ($instance->symptoms->hosp == 'yes') {
      $instance->symptoms->admission_date = $faker->dateTimeBetween('-70 days')->format('m-d-Y');

      if ($instance->symptoms->discharged == 'yes') {
        $instance->symptoms->discharged_date = $faker->dateTimeBetween('-50 days')->format('m-d-Y');
      }
    }

    if ($instance->symptoms->icu == 'yes') {
      $instance->symptoms->icu_date = $faker->dateTimeBetween('-50 days')->format('m-d-Y');
      $instance->symptoms->icu_proned = $faker->words(5, true);
      $instance->symptoms->icu_discharged = $faker->randomElement(self::$yesNoUnknown);
      $instance->symptoms->ecmo = $faker->randomElement(self::$yesNoUnknown);

      if ($instance->symptoms->discharged == 'yes') {
        $instance->symptoms->icu_discharged_date = $faker->dateTimeBetween('-50 days')->format('m-d-Y');
      }
    }

    if ($instance->symptoms->mechvent == 'yes') {
      $instance->symptoms->mechvent_dur = $faker->numberBetween(1, 5);
    }

    if ($instance->symptoms->death == 'yes') {
      $death_date_unknown = $faker->randomElement([ '', 'unknown']);
      if ($onset_unknown == '') {
        $instance->death_date = $faker->dateTimeBetween('-40 days')->format('m-d-Y');
      }
    }

    $instance->other_symptoms = [];
    $other_symptoms = $faker->numberBetween(0, 3);

    for ($i = 0; $i < $other_symptoms; $i++) {
      $instance->other_symptoms[] = $faker->words(5, true);
    }


    $instance->affected_family_members = (object) [
      'mother'        => $faker->randomElement(self::$affected),
      'father'        => $faker->randomElement(self::$affected),
      'brother'       => $faker->randomElement(self::$affected),
      'sister'        => $faker->randomElement(self::$affected),
      'spouse'        => $faker->randomElement(self::$affected),
      'child'         => $faker->randomElement(self::$affected),
      'aunt'          => $faker->randomElement(self::$affected),
      'uncle'         => $faker->randomElement(self::$affected),
      'cousin'        => $faker->randomElement(self::$affected),
      'grandmother'   => $faker->randomElement(self::$affected),
      'grandfather'   => $faker->randomElement(self::$affected),
      'grandchildren' => $faker->randomElement(self::$affected),
    ];

    $instance->nasopharyngleal_swab_samples = [];
    $nasopharyngleal_swab_samples = $faker->numberBetween(0, 3);
    for ($i = 0; $i < $nasopharyngleal_swab_samples; $i++) {
      $instance->nasopharyngleal_swab_samples[] = (object) [
        'swab_id'         => $faker->swiftBicNumber(),
        'swab_date'       => $faker->dateTimeBetween('-40 days')->format('m-d-Y'),
        'processing_date' => $faker->dateTimeBetween('-30 days')->format('m-d-Y'),
        'result'          => $faker->randomElement(self::$swabResult),
      ];
    }

    $instance->blood_samples = [];
    $blood_samples = $faker->numberBetween(0, 10);
    for ($i = 0; $i < $blood_samples; $i++) {
      $instance->blood_samples[] = (object) [
        'blood_id'         => $faker->swiftBicNumber(),
        'blood_date'       => $faker->dateTimeBetween('-90 days')->format('m-d-Y'),
        'blood_processing' => $faker->dateTimeBetween('-70 days')->format('m-d-Y'),
      ];
    }

    $instance->case_report_form_patient_data_complete = $faker->randomElement(self::$reportComplete);

    return $instance;
  }

}
