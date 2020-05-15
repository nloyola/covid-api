<?php

namespace App\Models;

class BloodTypes extends LabelledEnum {

  const Apositive  = [ 'id' => 1, 'label' => 'A+' ];
  const Bpositive  = [ 'id' => 2, 'label' => 'B+' ];
  const Opositive  = [ 'id' => 3, 'label' => 'O+' ];
  const ABpositive = [ 'id' => 4, 'label' => 'AB+' ];
  const Anegative  = [ 'id' => 5, 'label' => 'A-' ];
  const Bnegative  = [ 'id' => 6, 'label' => 'B-' ];
  const Onegative  = [ 'id' => 7, 'label' => 'O-' ];
  const ABnegative = [ 'id' => 8, 'label' => 'AB-' ];

}
