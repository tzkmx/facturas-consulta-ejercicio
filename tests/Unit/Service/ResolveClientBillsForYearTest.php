<?php

namespace Unit\Service;

use Jefrancomix\ConsultaFacturas\Request\RequestForYearInterface;
use Jefrancomix\ConsultaFacturas\RequestHandler\HandlerInterface;
use Jefrancomix\ConsultaFacturas\Service\ResolveClientBillsForYear;
use PHPUnit\Framework\TestCase;

/**
 * @group RequestHandlerRefactor
 */
class ResolveClientBillsForYearTest extends TestCase
{
    public function testCallsHandlerWithRequest()
    {
        $handler = $this->createMock(HandlerInterface::class);
        $request = $this->createMock(RequestForYearInterface::class);

        $service = new ResolveClientBillsForYear($handler);

        $handler->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($request));

        $service->getReport('testing', 2017);
    }
}
