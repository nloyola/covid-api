<?php

namespace App\Models;

class SwabResults extends LabelledEnum {

  const Positive = [ 'id' => 0, 'label' => 'positive'];
  const Negative = [ 'id' => 1, 'label' => 'negative'];
  const Pending = [ 'id' => 1, 'label' => 'pending'];
  const NotDone = [ 'id' => 1, 'label' => 'not_done'];
  const Indeterminate = [ 'id' => 1, 'label' => 'indeterminate'];

}
