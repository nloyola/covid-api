<?php

namespace App\Models;

class YesNoNaUnknown extends LabelledEnum {

  const No      = ['id' => 0, 'label' => 'no'];
  const Yes     = ['id' => 1, 'label' => 'yes'];
  const Unknown = ['id' => 9, 'label' => 'unknown'];
  const NotApplicable = ['id' => 10, 'label' => 'not_applicable'];


}
