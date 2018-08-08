<?php

namespace Jefrancomix\ConsultaFacturas\Exception;

trait ExceptionBuilder
{
    public static function getException(string $type, string $errorString, array $errorData)
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
