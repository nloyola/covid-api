<?php

namespace App\Validation;

class ValidationResult extends Validation {

    public function __construct($value) {
      parent::__construct([], $value);
    }

}
