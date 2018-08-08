<?php

namespace Jefrancomix\ConsultaFacturas\Exception;

class ExceptionBuilder
{
    public static function get(string $type, string $errorString, array $errorData)
    {
        $errorMsg = sprintf($errorString . ' Recibí: %s', implode(' => ', $errorData));
        if ($type === 'DateRange') {
            $exceptionClass = InvalidDateRangeException::class;
        } else {
            $exceptionClass = '\RuntimeException';
        }

        return new $exceptionClass($errorMsg);
    }
}