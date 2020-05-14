<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model {

  public $timestamps = false;
  protected $fillable = [ 'email', 'selector', 'token', 'reset_time' ];

  public static function makeSlugFromName($name) {
    return self::makeSlug($name);
  }

}
