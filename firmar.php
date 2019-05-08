<?php
require(dirname(__FILE__) . '/hacienda/firmador.php');

use Hacienda\Firmador;

$pfx            = ''; // Ruta del archivo de la llave criptográfica (*.p12)
$pin            = ''; // PIN de 4 dígitos de la llave criptográfica
$xml_sin_firmar = ''; // Ruta del archivo XML (comprobante electrónico)
$xml_firmado    = ''; // Ruta del nuevo arhivo XML (firmado XADES-EPES)

// Nuevo firmador
$firmador = new Firmador();

// Método para firmar comprobantes electrónicos
$firmador->firmarXml($pfx, $pin, $xml_sin_firmar, $xml_firmado);
