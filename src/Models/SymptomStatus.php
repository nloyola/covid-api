<?php

namespace App\Models;

class SymptomStatus extends LabelledEnum
{
  const Asymptomatic = ['id' => 0, 'label' => 'asymptomatic'];
  const Symptomatic  = ['id' => 1, 'label' => 'symptomatic'];
  const Unknown      = ['id' => 9, 'label' => 'unknown'];
}
