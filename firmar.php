<?php
require(dirname(__FILE__) . '/hacienda/firmador.php');

use Hacienda\Firmador;

$pfx    = ''; // Ruta del archivo de la llave criptográfica (*.p12)
$pin    = ''; // PIN de 4 dígitos de la llave criptográfica
$xml    = ''; // String XML ó Ruta del archivo XML (comprobante electrónico)
$ruta   = ''; // Ruta del nuevo arhivo XML cuando se desea guardar en disco

// Nuevo firmador
$firmador = new Firmador();

// Se firma XML y se recibe un string resultado en Base64
$base64 = $firmador->firmarXml($pfx, $pin, $xml, $firmador::TO_BASE64_STRING);
print_r($base64);

// Se firma XML y se recibe un string resultado en Xml
$xml_string = $firmador->firmarXml($pfx, $pin, $xml, $firmador::TO_XML_STRING);
print_r($xml_string);

// Se firma XML, se guarda en disco duro ($ruta) y se recibe el número de bytes del archivo guardado. En caso de error se recibe FALSE
$archivo = $firmador->firmarXml($pfx, $pin, $xml, $firmador::TO_XML_FILE, $ruta);
print_r($archivo);