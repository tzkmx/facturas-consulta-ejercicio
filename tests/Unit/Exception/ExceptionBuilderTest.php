<?php

namespace Unit\Exception;

use Jefrancomix\ConsultaFacturas\Exception\ExceptionBuilder;
use Jefrancomix\ConsultaFacturas\Exception\InvalidDateRangeException;
use PHPUnit\Framework\TestCase;


class ExceptionBuilderTest extends TestCase
{
    public function testGetDateExceptionWithArgs()
    {
        $argErrorData = ['first arg', 'second arg'];
        $shortcutExceptionClass = 'DateRange';
        $argErrorTemplate = 'Prueba de argumentos.';

        $expectedExceptionClass = InvalidDateRangeException::class;
        $expectedExceptionMessage = 'Prueba de argumentos. Recibí: first arg => second arg';

        $exception = ExceptionBuilder::get($shortcutExceptionClass, $argErrorTemplate, $argErrorData);

        $this->assertEquals($expectedExceptionClass, get_class($exception));
        $this->assertEquals($expectedExceptionMessage, $exception->getMessage());
    }
}
