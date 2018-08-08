<?php

namespace Integration;

use PHPUnit\Framework\TestCase;
use Jefrancomix\ConsultaFacturas\Service\ResolveClientBillsForYear;
use Jefrancomix\ConsultaFacturas\RequestHandler\PipelineHandler;

class ResolveClientBillsForYearTest extends TestCase
{
    public function testServiceReportsBillsAndQueriesFetched()
    {
        $handler = new PipelineHandler();
        $service = new ResolveClientBillsForYear($handler);
        $clientId = 'testing';
        $year = '2017';
        $report = $service->getReport($clientId, $year);

        $expectedReport = [
            'clientId' => 'testing',
            'year' => '2017',
            'queriesFetched' => 1,
            'billsIssued' => 99,
            'successQueries' => [
                [
                    'range' => [
                        'start' => '2017-01-01',
                        'finish' => '2017-12-31',
                    ],
                    'tries' => 1,
                    'billsIssued' => 99,
                ],
            ],
        ];
        $this->assertEquals($expectedReport, $report);
    }
}
