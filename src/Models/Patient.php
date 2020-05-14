<?php

namespace App\Models;

use App\Utils\LoggerFuncs;
use App\Utils\RedcapCaseReportConverter;
use App\Utils\RedcapPracticeConverter;
use stdClass;

class Patient
{
  use LoggerFuncs;
  use RedcapCaseReportConverter;
  use RedcapPracticeConverter;

  public static $covid19Status = [ 'positive', 'negative' ];
  public static $genders = [ 'unknown', 'other', 'female', 'male' ];
  public static $yesNo        = [ 'yes', 'no'];
  public static $yesNoUnknown = [ 'yes', 'no', 'unknown'];
  public static $medicalHistoryStatus = ['resolved', 'ongoing_with_treatment', 'ongoing_without_treatment'];
  public static $examination = [
    'normal',
    'abnormal_not_clinically_significant',
    'abnormal_clinically_significant',
    'not_assessed'
  ];

  public static $doseUnit = [
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

  public static $doseRoute = [
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

  public static $doseFrequency = [
    'QD',
    'BID',
    'TID',
    'QID',
    'PRN',
    'QMon/Wed/Fri',
    'QMonth',
    'Once',
  ];

  public static $symptomStatus = [
    'symptomatic',
    'asymptomatic',
    'unknown',
  ];

  public static $symptomResolution = [
    'symptom_resolved_unknown_date',
    'still_symptomatic',
    'unknown_symptom_status',
    'symptoms_resolved_with_date',
  ];

  public static $affected = [
    'covid_19_positive',
    'covid_19_negative',
    'not_tested',
    'unknown',
    'not_applicable',
    ];

  public static $swabResults = [
    'positive',
    'negative',
    'pending',
    'not_done',
    'indeterminate',
  ];

  public static $reportComplete = [
    'incomplete',
    'unverified',
    'complete',
  ];

  public function __clone() {
    $this->medical_history              = clone $this->medical_history;
    $this->relevant_history             = array_merge([],  $this->relevant_history);
    $this->physical_exam                = clone $this->physical_exam;
    $this->concomitant_medications      = array_merge([], $this->concomitant_medications);
    $this->symptoms                     = clone $this->symptoms;
    $this->other_symptoms               = array_merge([], $this->other_symptoms);
    $this->affected_family_members      = clone $this->affected_family_members;
    $this->nasopharyngleal_swab_samples = array_merge([], $this->nasopharyngleal_swab_samples);
    $this->blood_samples                = array_merge([], $this->blood_samples);
  }

  public function isCovid19Positive(): bool {
    return $this->current_status == 'positive';
  }

  public function isCovid19Negative(): bool {
    return $this->current_status == 'negative';
  }

  public function hasGenderMale(): bool {
    return $this->sex == 'male';
  }

  public function hasGenderFemale(): bool {
    return $this->sex == 'female';
  }

  public function hasGenderOther(): bool {
    return $this->sex == 'other';
  }

  public function hasGenderUnknown(): bool {
    return $this->sex == 'unknown';
  }

  public function symptomatic(): bool
  {
    return $this->symptoms->status == 'symptomatic';
  }

  public function hasComorbidity(): bool
  {
    if (!property_exists($this, 'medical_history')) {
      throw new \Error('no medical history in case report');
    }

    $conditions = array_map(
      function ($property) {
        return $this->medical_history->{$property} == 'yes';
      },
      [
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
      ]
    );

    $reduced =  array_reduce($conditions, function ($c, $i) {
      return $c || $i;
    });

    return $reduced || count($this->relevant_history) > 0;
  }

  public function abnormalPhisicalExam(): bool {
    if (!property_exists($this, 'physical_exam')) {
      throw new \Error('no physical exam in case report');
    }

    $possibleExamItems = array_map(
      function ($property) {
        return $this->physical_exam->{$property} == 'abnormal_clinically_significant';
      },
      [
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
      ]);

    $reduced =  array_reduce($possibleExamItems, function ($c, $i) {
      return $c || $i;
    });

    return $reduced;
  }

  public function testedPositive(): bool {
    $filtered = array_filter($this->nasopharyngleal_swab_samples, function ($sample) {
      return $sample->result == 'positive';
    });
    return count($filtered) > 0;
  }

  public static function fromStdClass(stdClass $record)
  {
    $instance = new self();

    $instance->record_id          = $record->record_id;
    $instance->phn                = $record->phn;
    $instance->current_status     = self::currentStatus($record->current_status);
    $instance->testing_date       = self::propertyOrNull($record, 'testing_date');
    $instance->positive_test_date = self::propertyOrNull($record, 'positive_test_date');
    $instance->sex                = self::numberToGender($record->sex_324500);
    $instance->bloodtype          = self::numberToBloodType($record->bloodtype);

    $instance->medical_history = (object) [
      'cld'                        => self::numberToYesNo($record->cld_yn),
      'diabetes'                   => self::numberToYesNo($record->diabetes_yn),
      'cvd'                        => self::numberToYesNo($record->cvd_yn),
      'prior_myocardial_infarctio' => self::numberToYesNo($record->prior_myocardial_infarctio),
      'prior_coronary_artery_bypa' => self::numberToYesNo($record->prior_coronary_artery_bypa),
      'prior_coronary_artery_bypa' => self::numberToYesNo($record->prior_coronary_artery_bypa),
      'prior_percutaneous_coronar' => self::numberToYesNo($record->prior_percutaneous_coronar),
      'renaldis'                   => self::numberToYesNo($record->renaldis_yn),
      'liverdis'                   => self::numberToYesNo($record->liverdis_yn),
      'immsupp'                    => self::numberToYesNo($record->immsupp_yn),
      'hyp'                        => self::numberToYesNo($record->hyp_yn),
      'hypertension'               => self::numberToYesNo($record->hypertension_yn),
      'hiv'                        => self::numberToYesNo($record->hiv),
      'cerebrovascular_disease'    => self::numberToYesNo($record->cerebrovascular_disease),
      'prior_stroke'               => self::numberToYesNo($record->prior_stroke),
      'obesity'                    => self::numberToYesNo($record->obesity),
      'dyslipidemia'               => self::numberToYesNo($record->dyslipidemia),
      'pregnant'                   => self::numberToYesNo($record->pregnant_yn),
      'smoke_curr'                 => self::numberToYesNo($record->smoke_curr_yn),
      'smoke_former'               => self::numberToYesNo($record->smoke_former_yn),
      'has_other_disease'          => self::numberToYesNo($record->otherdis_yn),
      'other_disease'              => self::propertyOrNull($record, 'if_other_please_specify'),
      'hba1c'                      => self::numberToYesNo($record->hba1c),
      'hba1c_result'               => self::numberToYesNo($record->hba1c_result),
      'date_of_most_recent_hba1c'  => self::propertyOrNull($record, 'date_of_most_recent_hba1c')
    ];

    $relevant_history = self::numberToYesNo($record->relevant_history, 2);

    if ($relevant_history == 'yes') {
      $instance->relevant_history = [
        self::extractMedicalHistoryOther($record, 1),
        self::extractMedicalHistoryOther($record, 2),
        self::extractMedicalHistoryOther($record, 3),
        self::extractMedicalHistoryOther($record, 4),
        self::extractMedicalHistoryOther($record, 5)
      ];
    } else {
      $instance->relevant_history = [];
    }

    $instance->physical_exam = (object) [
      'general_appearance'         => self::numberToExamination($record->general_appearance),
      'lungs_chest'                => self::numberToExamination($record->lungs_chest),
      'skin'                       => self::numberToExamination($record->skin),
      'head_ears_eyes_nose_throat' => self::numberToExamination($record->head_ears_eyes_nose_throat),
      'neck'                       => self::numberToExamination($record->neck),
      'lymph_nodes'                => self::numberToExamination($record->lymph_nodes),
      'genitourinary'              => self::numberToExamination($record->genitourinary),
      'heart'                      => self::numberToExamination($record->heart),
      'mouth'                      => self::numberToExamination($record->mouth),
      'abdomen_gastrointestinal'   => self::numberToExamination($record->abdomen_gastrointestinal),
      'extremities'                => self::numberToExamination($record->extremities),
      'neurological'               => self::numberToExamination($record->neurological),
      'musculoskeletal'            => self::numberToExamination($record->musculoskeletal),
      'thyroid'                    => self::numberToExamination($record->thyroid),
      'back_spinal'                => self::numberToExamination($record->back_spinal),
      'external_genitalia'         => self::numberToExamination($record->external_genitalia),
      'comments'                   => self::propertyOrNull($record, 'comments'),
      'height'                     => self::propertyOrNull($record, 'height'),
      'weight'                     => self::propertyOrNull($record, 'weight_kg'),
      'waist_circumference'        => self::propertyOrNull($record, 'waist_circumference_cm'),
      'date_of_physical_exam'      => self::propertyOrNull($record, 'date_of_physical_exam'),
    ];

    $concomitant_medications = (int) self::propertyOrNull($record, 'conmed_num');

    if ($concomitant_medications > 0) {
      $instance->concomitant_medications = [
        self::extractConcominantMedications($record, 1),
        self::extractConcominantMedications($record, 2),
        self::extractConcominantMedications($record, 3),
        self::extractConcominantMedications($record, 4),
        self::extractConcominantMedications($record, 5),
      ];
    } else {
      $instance->concomitant_medications = [];
    }

    $instance->symptoms = (object) [
      'status'               => self::numberToSymptomStatus($record->sympstatus),
      'onset_date'           => self::propertyOrNull($record, 'onset_dt'),
      'onset_unknown'        => self::propertyOrNull($record, 'onset_unk'),
      'resolution'           => self::numberToSymptomResolution($record->symp_res_yn),
      'resolution_date'      => self::propertyOrNull($record, 'symp_res_dt'),
      'fever'                => self::numberToYesNo($record->fever_yn),
      'subjective_fever'     => self::numberToYesNo($record->sfever_yn),
      'chills'               => self::numberToYesNo($record->chills_yn),
      'myalgia'              => self::numberToYesNo($record->myalgia_yn),
      'runny_nose'           => self::numberToYesNo($record->runnose_yn),
      'sore_throat'          => self::numberToYesNo($record->sthroat_yn),
      'cough'                => self::numberToYesNo($record->cough_yn),
      'sob'                  => self::numberToYesNo($record->sob_yn),
      'nauseavomit'          => self::numberToYesNo($record->nauseavomit_yn),
      'headache'             => self::numberToYesNo($record->headache_yn),
      'abdom'                => self::numberToYesNo($record->abdom_yn),
      'diarrhea'             => self::numberToYesNo($record->diarrhea_yn),
      'anosmia'              => self::numberToYesNo($record->anosmia_yn),
      'pneumonia'            => self::numberToYesNo($record->pna_yn),
      'acute_resp_distress'  => self::numberToYesNo($record->acuterespdistress_yn),
      'ards_date'            => self::propertyOrNull($record, 'ards_date'),
      'ards_resolution_date' => self::propertyOrNull($record, 'ards_date1'),
      'abxchest'             => self::numberToYesNo($record->abxchest_yn),
      'hosp'                 => self::numberToYesNo($record->hosp_yn),
      'admission_date'       => self::propertyOrNull($record, 'adm1_dt'),
      'discharged'           => self::numberToYesNo($record->discharged),
      'discharged_date'      => self::propertyOrNull($record, 'dis1_dt'),
      'icu'                  => self::numberToYesNo($record->icu_yn),
      'icu_date'             => self::propertyOrNull($record, 'icu_dt'),
      'icu_proned'           => self::propertyOrNull($record, 'was_the_patient_proned_in'),
      'icu_discharged'       => self::propertyOrNull($record, 'was_the_patient_discharged'),
      'icu_discharged_date'  => self::propertyOrNull($record, 'icu_dischaged'),
      'mechvent'             => self::numberToYesNo($record->mechvent_yn),
      'mechvent_dur'         => self::propertyOrNull($record, 'mechvent_dur'),
      'ecmo'                 => self::numberToYesNo($record->ecmo_yn),
      'death'                => self::numberToYesNo($record->death_yn),
      'death_date'           => self::propertyOrNull($record, 'death_dt'),
      'death_date_unknown'   => self::propertyOrNull($record, 'death_dt') == '1' ? 'unknown' : null,
    ];

    $instance->other_symptoms = array_filter(
      [
        self::propertyOrNull($record, 'othersym1_spec'),
        self::propertyOrNull($record, 'othersym2_spec'),
        self::propertyOrNull($record, 'othersym3_spec')
      ],
      function ($v) {
          return $v !== null;
      }
    );

    $instance->affected_family_members = (object) [
      'mother'        => self::numberToAffected($record->mother),
      'father'        => self::numberToAffected($record->father),
      'brother'       => self::numberToAffected($record->brother),
      'sister'        => self::numberToAffected($record->sister),
      'spouse'        => self::numberToAffected($record->spouse),
      'child'         => self::numberToAffected($record->child),
      'aunt'          => self::numberToAffected($record->aunt),
      'uncle'         => self::numberToAffected($record->uncle),
      'cousin'        => self::numberToAffected($record->cousin),
      'grandmother'   => self::numberToAffected($record->grandmother),
      'grandfather'   => self::numberToAffected($record->grandfather),
      'grandchildren' => self::numberToAffected($record->grandchild_ren),
    ];

    $instance->nasopharyngleal_swab_samples = [
      self::extractSwabSample($record, 1),
      self::extractSwabSample($record, 2),
      self::extractSwabSample($record, 3),
    ];

    $instance->blood_samples = [
      self::extractBloodSample($record, 1),
      self::extractBloodSample($record, 2),
      self::extractBloodSample($record, 3),
      self::extractBloodSample($record, 4),
      self::extractBloodSample($record, 5),
      self::extractBloodSample($record, 6),
      self::extractBloodSample($record, 7),
      self::extractBloodSample($record, 8),
      self::extractBloodSample($record, 9),
      self::extractBloodSample($record, 10),
    ];

    $instance->case_report_form_patient_data_complete =
      self::numberToCaseReportComplete($record->case_report_form_patient_data_complete);

    return $instance;
  }

  /**
   * @param string $json The "records" exported from RedCap's API.
   */
  public static function patientFromRedcapApiRecords(string $json)
  {
    $data = json_decode($json, false);
    $patients = [];

    foreach ($data as $record) {
      $patient = self::fromStdClass($record);
      self::dump('===> ', $patient);

      $patients[] = $patient;
    }
    return $patients;
  }
}
