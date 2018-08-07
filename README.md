[![Build Status](https://travis-ci.org/tzkmx/facturas-consulta-ejercicio.svg?branch=master)](https://travis-ci.org/tzkmx/facturas-consulta-ejercicio)
[![Maintainability](https://api.codeclimate.com/v1/badges/a5d4977f32e60338cf27/maintainability)](https://codeclimate.com/github/tzkmx/facturas-consulta-ejercicio/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/a5d4977f32e60338cf27/test_coverage)](https://codeclimate.com/github/tzkmx/facturas-consulta-ejercicio/test_coverage)

== Objetivo ==

El ejercicio consiste en obtener el total de facturas emitidas
en el período correspondiente a un año, para un "cliente".

Para ello, se proporciona el año, el Id del cliente y el endpoint
al que se realizan las consultas de cada período.

El reto consiste en que si la cantidad de facturas emitidas en
determinado período excede un umbral, no arroja el dato sino un
error de que son demasiadas, obligándonos a dividir la consulta
por períodos menores.


