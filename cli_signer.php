#!/usr/bin/env php

<?php
require(dirname(__FILE__) . '/hacienda/firmador.php');

use Hacienda\Firmador;

$pfx    = $argv[1]; // Ruta del archivo de la llave criptográfica (*.p12)
$pin    = $argv[2]; // PIN de 4 dígitos de la llave criptográfica
$xml    = $argv[3]; // String XML ó Ruta del archivo XML (comprobante electrónico)
$ruta   = $argv[4]; // Ruta del nuevo arhivo XML cuando se desea guardar en disco

// Nuevo firmador
$firmador = new Firmador();

echo "pfx $pfx, pin $pin, in $xml, out $ruta\n";

$archivo = $firmador->firmarXml($pfx, $pin, $xml, $firmador::TO_XML_FILE, $ruta);
print_r($archivo);
