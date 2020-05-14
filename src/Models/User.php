<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {

  use HasSlug;

  public $timestamps = true;
  protected $fillable = [ 'slug', 'name', 'email', 'password', 'pwd_reset_token', 'pwd_reset_time' ];

  public static function makeSlugFromName($name) {
    return self::makeSlug($name);
  }

}
