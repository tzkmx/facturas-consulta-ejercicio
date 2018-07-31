<?php

use PHPUnit\Framework\TestCase;

require __DIR__ . '/../src/QueriesRegistry.php';

use Jefrancomix\ConsultaFacturas\QueriesRegistry;

class QueriesRegistryTest extends TestCase
{
    public function testJustBuiltRegistryIsCompletedFalse()
    {
        $registry = new QueriesRegistry(2017, 'client');

        $justBuiltStatus = [
            'completed' => false,
          ];

        $this->assertEquals($justBuiltStatus, $registry->getStatus());
    }
}