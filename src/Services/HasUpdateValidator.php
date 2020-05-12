<?php

namespace App\Services;

trait HasUpdateValidator {

  protected $validator;
  protected $updatePropertyValidator;

  /**
   * Ensures that the $values array has a property key with the values defined in
   * $this->updatePropertyValidator.
   */
  protected function validateUpdatePropertyValues(array $values) {
    if ($this->updatePropertyValidator === null) {
      throw new \Error('updatePropertyValidator is null');
    }

    // first check that the 'property' value exists and is valid
    $check = array_filter($values, function ($key) {
      return $key === 'property';
    }, ARRAY_FILTER_USE_KEY);

    return $this->validator->validate($check, $this->updatePropertyValidator);
  }

}
