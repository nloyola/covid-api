<?php

namespace App\Models;

class GenderAlt extends LabelledEnum
{

  const Male    = ['id' => 1, 'label' => 'male'];
  const Female  = ['id' => 2, 'label' => 'female'];
  const Unknown = ['id' => 4, 'label' => 'unknown'];
  const Other   = ['id' => 5, 'label' => 'other'];
}
