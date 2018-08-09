<?php

namespace Jefrancomix\ConsultaFacturas\Exception;

trait ExceptionBuilder
{
    public static function getException(string $type, string $errorString, array $errorData)
    {
        $errorMsg = sprintf($errorString . ' Recibí: %s', implode(' => ', $errorData));
        switch ($type) {
            case 'DateRange':
                $exceptionClass = InvalidDateRangeException::class;
                break;
            default:
                $exceptionClass = \RuntimeException::class;
        }

        return new $exceptionClass($errorMsg);
    }
}
