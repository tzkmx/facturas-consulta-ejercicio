<?php

namespace Unit\Query;

use Jefrancomix\ConsultaFacturas\Dates\DateRange;
use Jefrancomix\ConsultaFacturas\Query\Query;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusEndpointError;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusPending;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusRangeExceededThreshold;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusResultOk;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function setUp()
    {
        $this->range = new DateRange('2017-01-01', '2017-12-31');
        $this->query = new Query($this->range);
    }
    
    public function testQueryInitialized()
    {
        $this->assertEquals($this->range, $this->query->range());
        $this->assertEquals(0, $this->query->tries());
        $this->assertEquals(0, $this->query->result());
        $this->assertEquals(new QueryStatusPending(), $this->query->status());
        $this->assertEquals('', $this->query->error());
    }

    public function testQueryResultOk()
    {
        $this->query->saveResult(20);

        $this->assertEquals(1, $this->query->tries());
        $this->assertEquals(20, $this->query->result());
        $this->assertEquals(new QueryStatusResultOk(), $this->query->status());
        $this->assertEquals('', $this->query->error());
    }

    public function testQueryRangeExceededThreshold()
    {
        $this->query->saveResult('Hay más de 100 resultados');

        $this->assertEquals(1, $this->query->tries());
        $this->assertEquals(0, $this->query->result());
        $this->assertEquals(new QueryStatusRangeExceededThreshold(), $this->query->status());
        $this->assertEquals('', $this->query->error());
    }

    public function testQueryRangeError()
    {
        // TODO: Define error with status codes, right now only test error is saved
        $this->query->saveResult('Error: Endpoint error');

        $this->assertEquals(1, $this->query->tries());
        $this->assertEquals(0, $this->query->result());
        $this->assertEquals(new QueryStatusEndpointError(), $this->query->status());
        $this->assertEquals('Error: Endpoint error', $this->query->error());
    }
}
