<?php

namespace App\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebAppSecret extends CliCommand {

  public function __construct() {
    parent::__construct('webAppSecret');
  }

  protected function configure(): void
  {
    parent::configure();
    $this->setName('webAppSecret');
    $this->setDescription('Generates a secret string to use for the web site');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $secret =  md5(microtime().rand());
    $output->writeln("  secret: $secret");
    return 0;
  }

}
