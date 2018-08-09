<?php

namespace Unit\Exception;

use Jefrancomix\ConsultaFacturas\Exception\ExceptionBuilder;
use Jefrancomix\ConsultaFacturas\Exception\InvalidDateRangeException;
use PHPUnit\Framework\TestCase;

class ExceptionBuilderTest extends TestCase
{
    public function testGetDateExceptionWithArgs()
    {
        $mock = $this->getMockForTrait(ExceptionBuilder::class);

        $exceptionType = 'DateRange';
        $exceptionMessage = 'Prueba de argumentos.';
        $exceptionArgs = ['first arg', 'second arg'];

        $expectedExceptionClass = InvalidDateRangeException::class;
        $expectedExceptionMessage = 'Prueba de argumentos. Recibí: first arg => second arg';

        $exception = $mock::getException($exceptionType, $exceptionMessage, $exceptionArgs);

        $this->assertEquals($expectedExceptionClass, get_class($exception));
        $this->assertEquals($expectedExceptionMessage, $exception->getMessage());
    }

    public function testGetRuntimeExceptionWithArgs()
    {
        $mock = $this->getMockForTrait(ExceptionBuilder::class);

        $exceptionType = 'Unknown';
        $exceptionMessage = 'Error no identificado.';
        $exceptionArgs = ['ze','ome'];

        $expectedExceptionClass = \RuntimeException::class;
        $expectedExceptionMessage = 'Error no identificado. Recibí: ze => ome';

        $exception = $mock::getException($exceptionType, $exceptionMessage, $exceptionArgs);

        $this->assertEquals($expectedExceptionClass, get_class($exception));
        $this->assertEquals($expectedExceptionMessage, $exception->getMessage());
    }
}
