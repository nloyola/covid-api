<?php

namespace App\Validation;

use App\Utils\LoggerFuncs;
use App\Factories\LoggerFactory;
use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{

  use LoggerFuncs;

  public function __construct(LoggerFactory $loggerFactory)
  {
    $this->logger = $loggerFactory->createInstance('validator');
  }

  public function validate(array $valuesByField, array $rules): Validation
  {
    $errors = [];
    foreach ($rules as $field => $rule) {
      try {
        $rule->setName(ucfirst($field))->assert($valuesByField[$field] ?? null);
      } catch (NestedValidationException $e) {
        $errors[$field] = $e->getMessages();
      }
    }

    if (!empty($errors)) {
      return new ValidationError($errors);
    }

    return new ValidationResult(true);
  }
}
