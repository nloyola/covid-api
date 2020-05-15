<?php

namespace App\Models;

class AgeRanges extends BasicEnum
{

  const range0To17  = [ 'id' =>  0, 'min' =>  0, 'max' => 18,  'label' => 'Ages 0 to 17' ];
  const range18to39 = [ 'id' =>  1, 'min' => 18, 'max' => 39,  'label' => 'Ages 18 to 39' ];
  const range40to59 = [ 'id' =>  2, 'min' => 40, 'max' => 59,  'label' => 'Ages 40 to 59' ];
  const range60to79 = [ 'id' =>  3, 'min' => 60, 'max' => 79,  'label' => 'Ages 60 to 79' ];
  const range80to99 = [ 'id' =>  4, 'min' => 80, 'max' => 99,  'label' => 'Ages 80 to 99' ];

  public static function getRangeForAge(int $age) {
    foreach (self::validValues() as $range) {
      if (($age >= $range['min']) && ($age <= $range['max'])) {
        return $range;
      }
    }
    throw \Error("no range found for age $age");
  }

}
