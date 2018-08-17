<?php

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
use Jefrancomix\ConsultaFacturas\RequestHandler\PendingQueriesHandler;
use Jefrancomix\ConsultaFacturas\RequestHandler\PipelineHandler;
use Jefrancomix\ConsultaFacturas\RequestHandler\SumIssuedBillsHandler;
use Jefrancomix\ConsultaFacturas\Service\ResolveClientBillsForYear;
use Jefrancomix\ConsultaFacturas\UserInterfaces\Command;
use Jefrancomix\ConsultaFacturas\UserInterfaces\CommandInput;

$client = new Client();

$pendingHandler = new PendingQueriesHandler($client);
$completeHandler = new SumIssuedBillsHandler();

$handler = new PipelineHandler($pendingHandler, $completeHandler);

$rangesFactory = new DateRangeFactory();
$queriesFactory = new QueryFactory($rangesFactory);

$service = new ResolveClientBillsForYear($handler, $queriesFactory);

$command = new Command($service);

$input = new CommandInput();

$input->clientId = getenv('clientId');
$input->year = getenv('year');
$input->endpoint = getenv('endpoint');
$output = $command->run($input);

echo $output;
