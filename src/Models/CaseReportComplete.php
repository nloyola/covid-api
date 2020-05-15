<?php

namespace App\Models;

class CaseReportComplete extends LabelledEnum {

  const Incomplete = [ 'id' => 0, 'label' => 'incomplete' ];
  const Unverified = [ 'id' => 1, 'label' => 'unverified' ];
  const Complete   = [ 'id' => 2, 'label' => 'complete' ];

}
