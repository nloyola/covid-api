<?php

namespace App\Dto;

use App\Constants;
use App\Models\User;

class UserDto {

  public function __construct(User $user) {
    $this->id        = $user->id;
    $this->version   = intval($user->version);
    $this->slug      = $user->slug;
    $this->name      = $user->name;
    $this->email     = $user->email;
    $this->createdAt = $user->created_at->format(Constants::$dateFormat);

    if ($user->updated_at) {
        $this->updatedAt = $user->updated_at->format(Constants::$dateFormat);
    }
  }

  public static function create(User $user) {
    return new UserDto($user);
  }

}
