<?php

namespace App\Validation;

abstract class Validation {

  private $errors;
  private $value;

  public function __construct(array $errors = [], $value = null) {
    $this->errors = $errors;
    $this->value = $value;
  }

  public function errors(): array {
    return $this->errors;
  }

  public function value() {
    if ($this->failed()) {
      throw new \Error("validation has errors: " . join(",\n", $this->errors));
    }
    return $this->value;
  }

  public function failed() {
    return !empty($this->errors);
  }

  public function success() {
    return empty($this->errors);
  }

}
