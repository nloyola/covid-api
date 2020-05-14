<?php

namespace App\Validation\Rules;

use App\Models\User;
use Respect\Validation\Rules\AbstractRule;

final class UserSlugExists extends AbstractRule {
  public function validate($input): bool {
    return User::where('slug', $input)->count() > 0;
  }
}
