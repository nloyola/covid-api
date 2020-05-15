<?php

namespace App\Utils;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

class Dump
{

  public static function dump(...$vars)
  {
    $cloner = new VarCloner();
    $dumper = new CliDumper();
    foreach ($vars as $var) {
      $dumper->dump($cloner->cloneVar($var));
    }
  }
}
