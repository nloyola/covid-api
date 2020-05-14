<?php

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

final class UserSlugExistsException extends ValidationException {

  public static $defaultTemplates = [
    self::MODE_DEFAULT => [
      self::STANDARD => 'UserSlug not found'
    ]
  ];
}
