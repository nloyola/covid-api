<?php

namespace App\Models;

use App\Utils\LoggerFuncs;
use App\Utils\RedcapPatientConverter;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
  use LoggerFuncs;
  use RedcapPatientConverter;

  /**
   * @param string $json The "records" exported from RedCap's API.
   */
  public static function patientFromRedcapApi(string $json) {
    $data = json_decode($json, false);
    $patients = [];

    foreach ($data as $record) {
      //self::varLog('===> ', $record);
      $patient = [
        'record_id'                    => $record->record_id,
        'phn'                          => $record->phn,
        'current_status'               => self::currentStatus($record->current_status),
        'testing_date'                 => $record->testing_date ?? null,
        'positive_test_date'           => $record->positive_test_date ?? null,
        'sex'                          => self::sex($record->sex_324500),
        'medical_history'              => [
          'cld'                        => self::numberToYesNo($record->cld_yn),
          'diabetes'                   => self::numberToYesNo($record->diabetes_yn),
          'cvd'                        => self::numberToYesNo($record->cvd_yn),
          'prior_myocardial_infarctio' => self::numberToYesNo($record->prior_myocardial_infarctio),
          'prior_coronary_artery_bypa' => self::numberToYesNo($record->prior_coronary_artery_bypa),
          'prior_coronary_artery_bypa' => self::numberToYesNo($record->prior_coronary_artery_bypa),
          'prior_percutaneous_coronar' => self::numberToYesNo($record->prior_percutaneous_coronar),
          'renaldis_yn'                => self::numberToYesNo($record->renaldis_yn),
          'liverdis_yn'                => self::numberToYesNo($record->liverdis_yn),
          'immsupp_yn'                 => self::numberToYesNo($record->immsupp_yn),
          'hyp_yn'                     => self::numberToYesNo($record->hyp_yn),
          'hypertension_yn'            => self::numberToYesNo($record->hypertension_yn),
          'hiv'                        => self::numberToYesNo($record->hiv),
          'cerebrovascular_disease'    => self::numberToYesNo($record->cerebrovascular_disease),
          'prior_stroke'               => self::numberToYesNo($record->prior_stroke),
          'obesity'                    => self::numberToYesNo($record->obesity),
          'dyslipidemia'               => self::numberToYesNo($record->dyslipidemia),
          'pregnant_yn'                => self::numberToYesNo($record->pregnant_yn),
          'smoke_curr_yn'              => self::numberToYesNo($record->smoke_curr_yn),
          'smoke_former_yn'            => self::numberToYesNo($record->smoke_former_yn),
          'hba1c'                      => self::numberToYesNo($record->hba1c),
          'hba1c_result'               => self::numberToYesNo($record->hba1c_result),
          'relevant_history'           => self::numberToYesNoAlt($record->relevant_history),
          'bloodtype'                  => self::numberToBloodType($record->bloodtype)
        ],
        'medical_history_other'        => [
          self::extractMedicalHistoryOther($record, 1),
          self::extractMedicalHistoryOther($record, 2),
          self::extractMedicalHistoryOther($record, 3),
          self::extractMedicalHistoryOther($record, 4),
          self::extractMedicalHistoryOther($record, 5)
        ],
        'physical_exam'                => [
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
          'comments'                   => self::propertyOrNull($record, 'comments') ? self::numberToExamination($record->comments) : null,
          'height'                     => self::propertyOrNull($record, 'height'),
          'weight'                     => self::propertyOrNull($record, 'weight_kg'),
          'waist_circumference'        => self::propertyOrNull($record, 'waist_circumference_cm'),
          'date_of_physical_exam'      => self::propertyOrNull($record, 'date_of_physical_exam'),
        ],
        'concomitant_medications' => [
          self::extractConcominantMedications($record, 1),
          self::extractConcominantMedications($record, 2),
          self::extractConcominantMedications($record, 3),
          self::extractConcominantMedications($record, 4),
          self::extractConcominantMedications($record, 5),
        ],
        'symptoms' => [
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
          'other_symptoms'       => [
            self::propertyOrNull($record, 'othersym1_spec'),
            self::propertyOrNull($record, 'othersym2_spec'),
            self::propertyOrNull($record, 'othersym3_spec')
          ],
          'pneumonia'            => self::numberToYesNo($record->pna_yn),
          'acute_resp_distress'  => self::numberToYesNo($record->acuterespdistress_yn),
          'ards_date'            => self::propertyOrNull($record, 'ards_date'),
          'ards_resolution_date' => self::propertyOrNull($record, 'ards_date1'),
          'abxchest'             => self::numberToYesNo($record->abxchest_yn),
          'hosp'                 => self::numberToYesNo($record->hosp_yn),
          'admission_date'       => self::propertyOrNull($record, 'adm1_dt'),
          'discharged'           => self::numberToYesNo($record, 'discharged'),
          'discharged_date'      => self::propertyOrNull($record, 'dis1_dt'),
          'discharged'           => self::numberToYesNo($record, 'discharged'),
        ],
      ];

      $patients[] = $patient;
    }
    return $patients;
  }
}
