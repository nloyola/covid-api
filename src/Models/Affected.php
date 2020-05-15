<?php

namespace App\Models;

class Affected extends LabelledEnum {

  const Covid19Positive = [ 'id' => 1, 'label' => 'covid_19_positive' ];
  const Covid19Negative = [ 'id' => 2, 'label' => 'covid_19_negative' ];
  const Nottested       = [ 'id' => 3, 'label' => 'not_tested' ];
  const Unknown         = [ 'id' => 4, 'label' => 'unknown' ];
  const Notapplicable   = [ 'id' => 5, 'label' => 'not_applicable' ];

}
