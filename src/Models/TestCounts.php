<?php

namespace App\Models;

class TestCounts
{
  /**
   * @var integer
   */
  public $positiveCount;

  /**
   * @var integer
   */
  public $negativeCount;

  public function __construct() {
    $this->positiveCount = 0;
    $this->negativeCount = 0;
  }

}
