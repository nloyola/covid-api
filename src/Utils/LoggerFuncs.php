<?php

namespace App\Utils;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

trait LoggerFuncs
{

  protected $logger;

  protected function varLog($message, ...$vars)
  {
    $this->logLevel = getenv('APP_LOG_LEVEL');

    if ($this->logLevel == 'debug') {
      $cloner = new VarCloner();
      $dumper = new CliDumper();
      $this->logger->debug($message . ': ');
      foreach ($vars as $var) {
        $dumper->dump($cloner->cloneVar($var));
      }
    }
  }

  protected static function dump(...$vars)
  {
    Dump::dump($vars);
  }
}
