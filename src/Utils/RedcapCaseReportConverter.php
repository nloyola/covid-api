<?php

namespace App\Utils;

use App\Models\DoseUnits;
use App\Models\DoseRoutes;
use App\Models\DoseFrequencies;
use App\Models\SwabResults;
use App\Models\YesNo;

trait RedcapCaseReportConverter {

  protected static function propertyOrNull($record, $property) {
    return property_exists($record, $property) ? $record->{$property} : null;
  }

  protected static function extractMedicalHistoryOther($record, $index) {
    $properties = [];
    switch ($index) {
      case 1:
        $properties = [
          'history'                  => 'med_hx_1',
          'condition'                => 'medhx_condition1',
          'condition_onset_date'     => 'condition_onsetdate1',
          'present_at_time_of_covid' => 'present_at_the_time_of_cov',
          'condition_worsened'       => 'was_this_condition_worsene',
          'status'                   => 'status'
        ];
        break;

      case 2:
        $properties = [
          'history'                  => 'med_hx_2',
          'condition'                => 'medhx_condition2',
          'condition_onset_date'     => 'condition_onsetdate11',
          'present_at_time_of_covid' => 'present_at_the_time_of_cov_2',
          'condition_worsened'       => 'was_this_condition_worsene_2',
          'status'                   => 'status_2'
        ];
        break;

      case 3:
        $properties = [
          'history'                  => 'med_hx_3',
          'condition'                => 'medhx_condition3',
          'condition_onset_date'     => 'condition_onsetdate12',
          'present_at_time_of_covid' => 'present_at_the_time_of_cov_3',
          'condition_worsened'       => 'was_this_condition_worsene_3',
          'status'                   => 'status_3'
        ];
        break;

      case 4:
        $properties = [
          'history'                  => 'med_hx_4',
          'condition'                => 'medhx_condition4',
          'condition_onset_date'     => 'condition_onsetdate13',
          'present_at_time_of_covid' => 'present_at_the_time_of_cov_4',
          'condition_worsened'       => 'was_this_condition_worsene_4',
          'status'                   => 'status_4'
        ];
        break;

      case 5:
        $properties = [
          'history'                  => 'med_hx_5',
          'condition'                => 'medhx_condition5',
          'condition_onset_date'     => 'condition_onsetdate14',
          'present_at_time_of_covid' => 'present_at_the_time_of_cov_5',
          'condition_worsened'       => 'was_this_condition_worsene_5',
          'status'                   => 'status_5'
        ];
        break;

      default:
        throw new \Error("invalid index for medical history other: $index");
    }

    $result = [];
    foreach ($properties as $new_property => $old_property) {
      $result[$new_property] = property_exists($record, $old_property) ? $record->{$old_property} : null;
    }
    return (object) $result;
  }

  protected static function extractConcominantMedications($record, $index) {
    $properties = [];
    switch ($index) {
      case 1:
        $properties = [
          'name'            => 'conmed_other_1',
          'indication'      => 'conmed_indication_1',
          'resp_complaint'  => 'conmed_respcomplaint_1',
          'dose_amt'        => 'conmed_dose_amt_1',
          'dose_unit'       => 'conmed_dose_unit_1',
          'dose_route'      => 'conmed_dose_route_1',
          'dose_frequency'  => 'conmed_dose_frequency_1',
          'dose_start_date' => 'conmed_dose_start_date_1',
          'dose_stop_check' => 'conmed_dose_stop_check_1',
          'dose_stop_date'  => 'conmed_dose_stop_date_1'
        ];
        break;

      case 2:
        $properties = [
          'name'            => 'conmed_other_2',
          'indication'      => 'conmed_indication_2',
          'resp_complaint'  => 'conmed_respcomplaint_2',
          'dose_amt'        => 'conmed_dose_amt_2',
          'dose_unit'       => 'conmed_dose_unit_2',
          'dose_route'      => 'conmed_dose_route_2',
          'dose_frequency'  => 'conmed_dose_frequency_2',
          'dose_start_date' => 'conmed_dose_start_date_2',
          'dose_stop_check' => 'conmed_dose_stop_check_2',
          'dose_stop_date'  =>'conmed_dose_stop_date_2'
        ];
        break;

      case 3:
        $properties = [
          'name'            => 'conmed_other_3',
          'indication'      => 'conmed_indication_3',
          'resp_complaint'  => 'conmed_respcomplaint_3',
          'dose_amt'        => 'conmed_dose_amt_3',
          'dose_unit'       => 'conmed_dose_unit_3',
          'dose_route'      => 'conmed_dose_route_3',
          'dose_frequency'  => 'conmed_dose_frequency_3',
          'dose_start_date' => 'conmed_dose_start_date_3',
          'dose_stop_check' => 'conmed_dose_stop_check_3',
          'dose_stop_date'  =>'conmed_dose_stop_date_3'
        ];
        break;

      case 4:
        $properties = [
          'name'            => 'conmed_other_4',
          'indication'      => 'conmed_indication_4',
          'resp_complaint'  => 'conmed_respcomplaint_4',
          'dose_amt'        => 'conmed_dose_amt_4',
          'dose_unit'       => 'conmed_dose_unit_4',
          'dose_route'      => 'conmed_dose_route_4',
          'dose_frequency'  => 'conmed_dose_frequency_4',
          'dose_start_date' => 'conmed_dose_start_date_4',
          'dose_stop_check' => 'conmed_dose_stop_check_4',
          'dose_stop_date'  =>'conmed_dose_stop_date_4'
        ];
        break;

      case 5:
        $properties = [
          'name'            => 'conmed_other_5',
          'indication'      => 'conmed_indication_5',
          'resp_complaint'  => 'conmed_respcomplaint_5',
          'dose_amt'        => 'conmed_dose_amt_5',
          'dose_unit'       => 'conmed_dose_unit_5',
          'dose_route'      => 'conmed_dose_route_5',
          'dose_frequency'  => 'conmed_dose_frequency_5',
          'dose_start_date' => 'conmed_dose_start_date_5',
          'dose_stop_check' => 'conmed_dose_stop_check_5',
          'dose_stop_date'  =>'conmed_dose_stop_date_5'
        ];
        break;

      default:
        throw new \Error("invalid index for concominant medication: $index");
    }

    $result = [];
    foreach ($properties as $new_property => $old_property) {
      $result[$new_property] = property_exists($record, $old_property) ? $record->{$old_property} : null;
    }

    $result['resp_complaint']  = new YesNo($result['resp_complaint']);
    $result['dose_unit']       = new DoseUnits($result['dose_unit']);
    $result['dose_route']      = new DoseRoutes($result['dose_route']);
    $result['dose_frequency']  = new DoseFrequencies($result['dose_frequency']);
    $result['dose_stop_check'] = new YesNo($result['dose_stop_check']);

    return (object) $result;
  }

  protected static function extractSwabSample($record, $index) {
    $properties = [];
    switch ($index) {
      case 1:
        $properties = [
          'swab_id'         => 'spec_npswab1id',
          'swab_date'       => 'spec_npswab1_dt',
          'processing_date' => 'np_processing',
          'result'          => 'spec_npswab1stateresult'
        ];
        break;

      case 2:
        $properties = [
          'swab_id'         => 'spec_npswab1id_2',
          'swab_date'       => 'spec_npswab2_dt',
          'processing_date' => 'np_processing_2',
          'result'          => 'spec_npswab2stateresult'
        ];
        break;

      case 3:
        $properties = [
          'swab_id'         => 'spec_npswab3id',
          'swab_date'       => 'spec_npswab3_dt',
          'processing_date' => 'np_processing_3',
          'result'          => 'spec_npswab3stateresult'
        ];
        break;

      default:
        throw new \Error("invalid index for concominant medication: $index");
    }

    $result = [];
    foreach ($properties as $new_property => $old_property) {
      $result[$new_property] = property_exists($record, $old_property) ? $record->{$old_property} : null;
    }

    $result['result']  = new SwabResults($result['result']);
    return (object) $result;
  }

  protected static function extractBloodSample($record, $index) {
    $properties = [];
    switch ($index) {
      case 1:
        $properties = [
          'blood_id' => 'blood_id',
          'blood_date' => 'blood_date',
          'blood_processing' => 'blood_processing',
        ];
        break;

      case 2:
      case 3:
      case 4:
      case 5:
      case 6:
      case 7:
      case 8:
      case 9:
      case 10:
        $properties = [
          'blood_id'         => "blood_id_$index",
          'blood_date'       => "blood_date_$index",
          'blood_processing' => "blood_processing_$index",
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

}
