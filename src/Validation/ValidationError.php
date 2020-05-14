<?php

namespace App\Validation;

class ValidationError extends Validation {

    public function __construct($errors) {
      if (is_array($errors)) {
        parent::__construct($errors);
      } else {
        parent::__construct([ $errors ]);
      }
    }

}
