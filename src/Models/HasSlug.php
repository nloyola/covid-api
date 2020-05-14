<?php

namespace App\Models;

use Illuminate\Support\Str as Str;

trait HasSlug {

  protected static function makeSlug($value) {
    $slug = Str::slug($value);
    $count = self::whereRaw("slug REGEXP '^{$slug}(-[0-9]+)?$'")->count();
    return $count ? "{$slug}-{$count}" : $slug;
  }

}
