<?php

namespace App\Models;

class MedicalHistoryStatus extends LabelledEnum
{

  const Resolved                = ['id' => 1, 'label' => 'resolved'];
  const OngoingWithTreatment    = ['id' => 2, 'label' => 'ongoing_with_treatment'];
  const OngoingWithoutTreatment = ['id' => 3, 'label' => 'ongoing_without_treatment'];
}
