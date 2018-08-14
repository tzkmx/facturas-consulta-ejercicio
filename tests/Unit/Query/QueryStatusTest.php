<?php

namespace Unit\Query;

use Jefrancomix\ConsultaFacturas\Query\QueryStatus;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusEndpointError;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusPending;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusRangeExceededThreshold;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusResultOk;
use PHPUnit\Framework\TestCase;

class QueryStatusTest extends TestCase
{
    /**
     * @dataProvider getStatusObjects
     * @param $constant int constant in QueryStatus interface
     * @param $queryStatusObject QueryStatus
     */
    public function testQueryStatus(int $constant, QueryStatus $queryStatusObject)
    {
        $this->assertEquals($constant, $queryStatusObject->value());
    }
    public function getStatusObjects()
    {
        return [
            [ QueryStatus::PENDING, new QueryStatusPending() ],
            [ QueryStatus::RESULT_OK, new QueryStatusResultOk() ],
            [ QueryStatus::RANGE_EXCEEDED_THRESHOLD, new QueryStatusRangeExceededThreshold() ],
            [ QueryStatus::ENDPOINT_ERROR, new QueryStatusEndpointError() ],
        ];
    }
}
