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

  public static $reportComplete = [
    'incomplete',
    'unverified',
    'complete',
  ];

  public static $comorbidities =
  [
    'cld',
    'diabetes',
    'cvd',
    'prior_myocardial_infarctio',
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
    return $this->current_status == TestStatus::Positive;
  }

  public function isCovid19Negative(): bool {
    return $this->current_status == TestStatus::Negative;
  }

  public function hasGenderMale(): bool {
    return $this->sex == Gender::Male;
  }

  public function hasGenderFemale(): bool {
    return $this->sex == Gender::Female;
  }

  public function hasGenderOther(): bool {
    return $this->sex == Gender::Other;
  }

  public function hasGenderUnknown(): bool {
    return $this->sex == Gender::Unknown;
  }

  /**
   * @param Gender $gender
   */
  public function hasGender($gender): bool {
    return $this->sex == $gender;
  }

  public function symptomatic(): bool
  {
    return $this->symptoms->status == SymptomStatus::Symptomatic;
  }

  public function hasComorbidity(): bool
  {
    if (!property_exists($this, 'medical_history')) {
      throw new \Error('no medical history in case report');
    }

    $conditions = array_map(
      function ($property) {
        return $this->medical_history->{$property} == YesNoUnknown::Yes;
      },
      self::$comorbidities
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

  public function swabTestedPositive(): bool {
    $filtered = array_filter($this->nasopharyngleal_swab_samples, function ($sample) {
      return $sample->result == SwabResults::Positive;
    });
    return count($filtered) > 0;
  }

  public function swabTestedNegative(): bool {
    $filtered = array_filter($this->nasopharyngleal_swab_samples, function ($sample) {
      return $sample->result == SwabResults::Negative;
    });
    return count($filtered) > 0;
  }

  public static function fromStdClass(stdClass $record)
  {
    $instance = new self();

    //\App\Utils\Dump::dump($record);

    // FIXME: add age once available from RedCAP

    $instance->record_id          = $record->record_id;
    $instance->phn                = self::propertyOrNull($record, 'phn');
    $instance->current_status     = new TestStatus($record->current_status);
    $instance->testing_date       = self::propertyOrNull($record, 'testing_date');
    $instance->positive_test_date = self::propertyOrNull($record, 'positive_test_date');
    $instance->sex                = new Gender($record->sex_324500);
    $instance->bloodtype          = new BloodTypes($record->bloodtype);

    $instance->medical_history = (object) [
      'cld'                        => new YesNoUnknown($record->cld_yn),
      'diabetes'                   => new YesNoUnknown($record->diabetes_yn),
      'cvd'                        => new YesNoUnknown($record->cvd_yn),
      'prior_myocardial_infarctio' => new YesNoUnknown($record->prior_myocardial_infarctio),
      'prior_coronary_artery_bypa' => new YesNoUnknown($record->prior_coronary_artery_bypa),
      'prior_coronary_artery_bypa' => new YesNoUnknown($record->prior_coronary_artery_bypa),
      'prior_percutaneous_coronar' => new YesNoUnknown($record->prior_percutaneous_coronar),
      'renaldis'                   => new YesNoUnknown($record->renaldis_yn),
      'liverdis'                   => new YesNoUnknown($record->liverdis_yn),
      'immsupp'                    => new YesNoUnknown($record->immsupp_yn),
      'hyp'                        => new YesNoUnknown($record->hyp_yn),
      'hypertension'               => new YesNoUnknown($record->hypertension_yn),
      'hiv'                        => new YesNoUnknown($record->hiv),
      'cerebrovascular_disease'    => new YesNoUnknown($record->cerebrovascular_disease),
      'prior_stroke'               => new YesNoUnknown($record->prior_stroke),
      'obesity'                    => new YesNoUnknown($record->obesity),
      'dyslipidemia'               => new YesNoUnknown($record->dyslipidemia),
      'pregnant'                   => new YesNoUnknown($record->pregnant_yn),
      'smoke_curr'                 => new YesNoUnknown($record->smoke_curr_yn),
      'smoke_former'               => new YesNoUnknown($record->smoke_former_yn),
      'has_other_disease'          => new YesNoUnknown($record->otherdis_yn),
      'other_disease'              => self::propertyOrNull($record, 'if_other_please_specify'),
      'hba1c'                      => new YesNoUnknown($record->hba1c),
      'hba1c_result'               => self::propertyOrNull($record, 'hba1c_result'),
      'date_of_most_recent_hba1c'  => self::propertyOrNull($record, 'date_of_most_recent_hba1c')
    ];

    $relevant_history = new YesNoAlt($record->relevant_history);
    if ($relevant_history == YesNoAlt::Yes) {
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
      'general_appearance'         => new Examination($record->general_appearance),
      'lungs_chest'                => new Examination($record->lungs_chest),
      'skin'                       => new Examination($record->skin),
      'head_ears_eyes_nose_throat' => new Examination($record->head_ears_eyes_nose_throat),
      'neck'                       => new Examination($record->neck),
      'lymph_nodes'                => new Examination($record->lymph_nodes),
      'genitourinary'              => new Examination($record->genitourinary),
      'heart'                      => new Examination($record->heart),
      'mouth'                      => new Examination($record->mouth),
      'abdomen_gastrointestinal'   => new Examination($record->abdomen_gastrointestinal),
      'extremities'                => new Examination($record->extremities),
      'neurological'               => new Examination($record->neurological),
      'musculoskeletal'            => new Examination($record->musculoskeletal),
      'thyroid'                    => new Examination($record->thyroid),
      'back_spinal'                => new Examination($record->back_spinal),
      'external_genitalia'         => new Examination($record->external_genitalia),
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
      'status'               => new SymptomStatus($record->sympstatus),
      'onset_date'           => self::propertyOrNull($record, 'onset_dt'),
      'onset_unknown'        => self::propertyOrNull($record, 'onset_unk'),
      'resolution'           => new SymptomResolutions($record->symp_res_yn),
      'resolution_date'      => self::propertyOrNull($record, 'symp_res_dt'),
      'fever'                => new YesNoUnknown($record->fever_yn),
      'subjective_fever'     => new YesNoUnknown($record->sfever_yn),
      'chills'               => new YesNoUnknown($record->chills_yn),
      'myalgia'              => new YesNoUnknown($record->myalgia_yn),
      'runny_nose'           => new YesNoUnknown($record->runnose_yn),
      'sore_throat'          => new YesNoUnknown($record->sthroat_yn),
      'cough'                => new YesNoUnknown($record->cough_yn),
      'sob'                  => new YesNoUnknown($record->sob_yn),
      'nauseavomit'          => new YesNoUnknown($record->nauseavomit_yn),
      'headache'             => new YesNoUnknown($record->headache_yn),
      'abdom'                => new YesNoUnknown($record->abdom_yn),
      'diarrhea'             => new YesNoUnknown($record->diarrhea_yn),
      'anosmia'              => new YesNoUnknown($record->anosmia_yn),
      'pneumonia'            => new YesNoUnknown($record->pna_yn),
      'acute_resp_distress'  => new YesNoUnknown($record->acuterespdistress_yn),
      'ards_date'            => self::propertyOrNull($record, 'ards_date'),
      'ards_resolution_date' => self::propertyOrNull($record, 'ards_date1'),
      'abxchest'             => new YesNoUnknown($record->abxchest_yn),
      'hosp'                 => new YesNoUnknown($record->hosp_yn),
      'admission_date'       => self::propertyOrNull($record, 'adm1_dt'),
      'discharged'           => new YesNoNaUnknown($record->discharged),
      'discharged_date'      => self::propertyOrNull($record, 'dis1_dt'),
      'icu'                  => new YesNoUnknown($record->icu_yn),
      'icu_date'             => self::propertyOrNull($record, 'icu_dt'),
      'icu_proned'           => self::propertyOrNull($record, 'was_the_patient_proned_in'),
      'icu_discharged'       => self::propertyOrNull($record, 'was_the_patient_discharged'),
      'icu_discharged_date'  => self::propertyOrNull($record, 'icu_dischaged'),
      'mechvent'             => new YesNoUnknown($record->mechvent_yn),
      'mechvent_dur'         => self::propertyOrNull($record, 'mechvent_dur'),
      'ecmo'                 => new YesNoUnknown($record->ecmo_yn),
      'death'                => new YesNoUnknown($record->death_yn),
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
      'mother'        => new Affected($record->mother),
      'father'        => new Affected($record->father),
      'brother'       => new Affected($record->brother),
      'sister'        => new Affected($record->sister),
      'spouse'        => new Affected($record->spouse),
      'child'         => new Affected($record->child),
      'aunt'          => new Affected($record->aunt),
      'uncle'         => new Affected($record->uncle),
      'cousin'        => new Affected($record->cousin),
      'grandmother'   => new Affected($record->grandmother),
      'grandfather'   => new Affected($record->grandfather),
      'grandchildren' => new Affected($record->grandchild_ren),
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
      new CaseReportComplete($record->case_report_form_patient_data_complete);

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
      $patients[] = $patient;
    }
    return $patients;
  }
}
