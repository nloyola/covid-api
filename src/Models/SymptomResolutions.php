<?php

namespace App\Models;

class SymptomResolutions extends LabelledEnum {

  const SymptomResolvedUnknownDate = [ 'id' => 0, 'label'  => 'symptom_resolved_unknown_date' ];
  const StillSymptomatic           = [ 'id' => 1, 'label'  => 'still_symptomatic' ];
  const UnknownSymptomStatus       = [ 'id' => 9, 'label'  => 'unknown_symptom_status' ];
  const SymptomsResolvedWithDate   = [ 'id' => 10, 'label' => 'symptoms_resolved_with_date' ];

}
