<?php

namespace App\Utils;

function numberToDiseaseStatus($number)
{
  switch ($number) {
    case '1': return 'resolved';
    case '2': return 'ongoing_with_treatment';
    case '3': return 'ongoing_without_treatment';
  }
}

trait RedcapPracticeConverter {

  protected static function numberToRace(string $number)
  {
      switch ($number) {
        case '1': return 'asian';
        case '2': return 'black';
        case '3': return 'caucasian';
        case '4': return 'indigenous_first_nations_inuit';
        case '5': return 'indian';
        case '6': return 'unknown';
        case '7': return 'other';
        default:
          return null;
      }
  }

  protected static function extractMedicalHistory($record, $index)
  {
    $properties = [];
    switch ($index) {
      case 1:
      case 2:
      case 3:
        $properties = [
          'med_history'              => "med_history_$index",
          'diagnosis_condition_surg' => "diagnosis_condition_surg_$index",
          'date_of_onset'            => "date_of_onset_$index",
          'dx_status'                => numberToDiseaseStatus("dx_status_$index"),
        ];
        break;

      default:
        throw new \Error("invalid index for concominant medication: $index");
    }

    $result = [];
    foreach ($properties as $new_property => $old_property) {
      $result[$new_property] = property_exists($record, $old_property) ? $record->{$old_property} : null;
    }

    return (object) $result;
  }

  protected static function extractPracticeConcominantMedications($record, $index) {
    $properties = [];
    switch ($index) {
      case 1:
        $properties = [
          'indication'         => "indication_1",
          'for_resp_complaint' => "for_resp_complaint_1",
          'dose_amount'        => "dose_amount_1",
          'dose_route'         => "dose_route_1",
          'dose_frequency'     => "dose_frequency",
          'start_date'         => "start_date_1",
        ];
        break;
      case 2:
        $properties = [
          'indication'         => "indication_2",
          'for_resp_complaint' => "for_resp_complaint_2",
          'dose_amount'        => "dose_amount_2",
          'dose_route'         => "dose_route_2",
          'dose_frequency'     => "dose_frequency_2",
          'start_date'         => "start_date_2",
        ];
        break;

      default:
        throw new \Error("invalid index for concominant medication: $index");
    }

    $result = [];
    foreach ($properties as $new_property => $old_property) {
      $result[$new_property] = property_exists($record, $old_property) ? $record->{$old_property} : null;
    }

    $result['for_resp_complaint']  = self::numberToYesNo($result['for_resp_complaint']);

    return (object) $result;
  }
}
