<?php

namespace App\Console;

use Symfony\Component\Console\Command\Command;

Abstract class CliCommand extends Command {

  public $config;

  public function __construct(?string $name = null) {
    parent::__construct($name);
  }
}
