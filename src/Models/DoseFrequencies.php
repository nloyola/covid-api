<?php

namespace App\Models;

class DoseFrequencies extends LabelledEnum
{
  const QD         = [ 'id' => 0, 'label' => 'QD'];
  const BID        = [ 'id' => 1, 'label' => 'BID'];
  const TID        = [ 'id' => 2, 'label' => 'TID'];
  const QID        = [ 'id' => 3, 'label' => 'QID'];
  const PRN        = [ 'id' => 4, 'label' => 'PRN'];
  const QMonWedFri = [ 'id' => 9, 'label' => 'QMon/Wed/Fri'];
  const QMonth     = [ 'id' => 10, 'label' => 'QMonth'];
  const Once       = [ 'id' => 11, 'label' => 'Once'];

}
