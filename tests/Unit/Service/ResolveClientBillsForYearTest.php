<?php

namespace Unit\Service;

use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
use Jefrancomix\ConsultaFacturas\Request\RequestForYearInterface;
use Jefrancomix\ConsultaFacturas\RequestHandler\HandlerInterface;
use Jefrancomix\ConsultaFacturas\Service\ResolveClientBillsForYear;
use PHPUnit\Framework\TestCase;

class ResolveClientBillsForYearTest extends TestCase
{
    public function testCallsHandlerWithRequest()
    {
        $this->markTestIncomplete('too much mocks?');
        $handler = $this->createMock(HandlerInterface::class);
        $request = $this->createMock(RequestForYearInterface::class);
        $queriesMock = $this->createMock(QueryFactory::class);

        $service = new ResolveClientBillsForYear($handler, $queriesMock);

        // implementar Request completa
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($request));

        $service->getReport('testing', 2017);
    }
}
