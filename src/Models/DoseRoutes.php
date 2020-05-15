<?php

namespace App\Models;

class DoseRoutes extends LabelledEnum
{
  const GT  = ['id' => 0,  'label' => 'GT'];
  const NG  = ['id' => 1,  'label' => 'NG'];
  const TO  = ['id' => 3,  'label' => 'TO'];
  const IM  = ['id' => 4,  'label' => 'IM'];
  const IV  = ['id' => 5,  'label' => 'IV'];
  const NAS = ['id' => 6,  'label' => 'NAS'];
  const PO  = ['id' => 7,  'label' => 'PO'];
  const IN  = ['id' => 8,  'label' => 'IN'];
  const SQ  = ['id' => 9,  'label' => 'SQ'];
  const RE  = ['id' => 10, 'label' => 'RE'];
  const IH  = ['id' => 11, 'label' => 'IH'];
}
