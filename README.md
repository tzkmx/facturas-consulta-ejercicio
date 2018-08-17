[![Build Status](https://travis-ci.org/tzkmx/facturas-consulta-ejercicio.svg?branch=master)](https://travis-ci.org/tzkmx/facturas-consulta-ejercicio)
[![Maintainability](https://api.codeclimate.com/v1/badges/a5d4977f32e60338cf27/maintainability)](https://codeclimate.com/github/tzkmx/facturas-consulta-ejercicio/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/a5d4977f32e60338cf27/test_coverage)](https://codeclimate.com/github/tzkmx/facturas-consulta-ejercicio/test_coverage)

## Objetivo

El ejercicio consiste en obtener el total de facturas emitidas
en el período correspondiente a un año, para un "cliente".

Para ello, se proporciona el año, el Id del cliente y el endpoint
al que se realizan las consultas de cada período.

El reto consiste en que si la cantidad de facturas emitidas en
determinado período excede un umbral, no arroja el dato sino un
error de que son demasiadas, obligándonos a dividir la consulta
por períodos menores.

## Ejecución

Actualmente se proporciona un cliente de prueba en línea de comandos,
se ejecuta pasando como argumentos a `testCli.php` el id, el año y
el extremo al que se realizarán las consultas

    php testCli.php ID-DEL-CLIENTE anio http://example.com

En caso fallido el comando requiere los argumentos en el orden indicado.

Se require previamente que se instalen las dependencias con composer:

    composer install

o

    composer install --no-dev

para no usar dependencias de desarrollo, como se muestra en el Dockerfile

## Pruebas

Se requiere PHPUnit, en caso de no estar habilitado globalmente,
al instalar las dependencias de desarrollo, el cliente está disponible
en

    vendor/bin/phpunit


En el phpunit.xml.dist están las indicaciones necesarias para que ejecute las pruebas.


## Roadmap

- Paralelizar consultas
- Simplificar red de objetos
- Mejorar algoritmo de búsqueda de rangos óptimos
- Completar Dockerfile para deploy
