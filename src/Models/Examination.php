<?php

namespace App\Models;

class Examination extends LabelledEnum {

  const Normal                           = [ 'id' => 1, 'label' => 'normal' ];
  const AbnormalNotClinicallySignificant = [ 'id' => 2, 'label' => 'abnormal_not_clinically_significant' ];
  const AbnormalClinicallySignificant    = [ 'id' => 3, 'label' => 'abnormal_clinically_significant' ];
  const NotAssessed                      = [ 'id' => 4, 'label' => 'not_assessed' ];

}
