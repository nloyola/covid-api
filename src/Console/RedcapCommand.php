<?php

namespace App\Console;

use App\Factories\LoggerFactory;
use App\Models\Patient;
use App\Utils\LoggerFuncs;
use Psr\Container\ContainerInterface;
use Selective\Config\Configuration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RedcapCommand extends CliCommand {

  use LoggerFuncs;

  public function __construct(ContainerInterface $container) {
    parent::__construct('redcap');

    $config = $container->get(Configuration::class);
    $this->apiUrl = $config->getString('redcap.api_url');
    $this->apiToken = $config->getString('redcap.api_token');

    $this->logger = $container->get(LoggerFactory::class)
      ->addConsoleHandler(1)
      ->createInstance('RedcapCommand');
  }

  protected function configure(): void
  {
    parent::configure();
    $this->setName('redcap');
    $this->setDescription('Uses the RedCap API to retrieve data');
    $this->addArgument('data', InputArgument::REQUIRED, 'the data to export');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $io = new SymfonyStyle($input, $output);

    switch ($input->getArgument('data')) {
      case 'events': return $this->exportEvents($input, $output);
      case 'metadata': return $this->exportMetadata($input, $output);
      case 'records': return $this->exportRecords($input, $output);
    }

    $io->error(['invalid parameter for "data"']);
    return 0;
  }

  private function exportEvents(InputInterface $input, OutputInterface $output): int
  {
    $output = $this->apiCall([
	'content' => 'event',
	'format'  => 'json',
	'arms'    => []
    ]);

    $this->varLog('events', $output);
    return 0;
  }

  private function exportMetadata(InputInterface $input, OutputInterface $output): int {
    $output = $this->apiCall([
      'content' => 'metadata',
      //'format'  => 'json'
      'format'  => 'csv'
    ]);

    $this->varLog('metadata', $output);
    return 0;
  }

  private function exportRecords(InputInterface $input, OutputInterface $output): int {
    $records = $this->apiCall([
      'content' => 'record',
      'format'  => 'json',
      'type'    => 'flat'
    ]);

    Patient::patientFromRedcapApiRecords($records);
    return 0;
  }

  private function apiCall(array $fields): string {
    $fields = array_merge($fields, [ 'token'   => $this->apiToken ]);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields, '', '&'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // Set to TRUE for production use
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);


    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
  }
}
