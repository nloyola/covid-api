<?php

namespace App\Models;

class YesNoUnknown extends LabelledEnum
{

  const No      = ['id' => 0, 'label' => 'no'];
  const Yes     = ['id' => 1, 'label' => 'yes'];
  const Unknown = ['id' => 9, 'label' => 'unknown'];
}
