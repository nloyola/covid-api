<?php

namespace App\Console;

use App\Factories\LoggerFactory;
use App\Test\EntityFactory;
use App\Utils\LoggerFuncs;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FakeCommand extends CliCommand {

  use LoggerFuncs;

  public function __construct(ContainerInterface $container) {
    parent::__construct('fake');

    $this->logger = $container->get(LoggerFactory::class)
      ->addConsoleHandler(1)
      ->createInstance('FakeCommand');
  }

  protected function configure(): void
  {
    parent::configure();
    $this->setName('fake');
    $this->setDescription('Creates a fake entity');
    $this->addArgument('entity', InputArgument::REQUIRED, 'the fake entity to create');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $io = new SymfonyStyle($input, $output);

    switch ($input->getArgument('entity')) {
      case 'patient': return $this->fakePatient($input, $output);
    }

    $io->error(['invalid parameter for "entity"']);
    return 0;
  }

  private function fakePatient(InputInterface $input, OutputInterface $output): int
  {
    $factory = new EntityFactory();
    $patient = $factory->patient();
    $this->varLog('patient', $patient);
    return 0;
  }
}
