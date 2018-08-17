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

if (count($argv) > 2) {
    $fill = array_fill(0, 2, '');
    $args = array_slice(array_merge(array_slice($argv, 1, 3), $fill), 0, 3);

    try {
        $input = new CommandInput(...$args);
    } catch (InvalidArgumentException $e) {
        echo "Error en argumentos:\n{$e->getMessage()}\n";
        exit(1);
    } catch (ArgumentCountError $e) {
        echo "Te faltan argumentos, la sintaxis correcta es\n\n",
          "php ", __FILE__, " ID-DE-CLIENTE año http://example.com/endpoint\n";
    }

    $output = $command->run($input);

    echo $output;
}
