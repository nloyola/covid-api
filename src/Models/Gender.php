<?php

namespace App\Models;

class Gender extends LabelledEnum {

  const Male    = [ 'id' => 1, 'label' => 'male' ];
  const Female  = [ 'id' => 2, 'label' => 'female' ];
  const Other   = [ 'id' => 3, 'label' => 'other' ];
  const Unknown = [ 'id' => 9, 'label' => 'unknown' ];

}
