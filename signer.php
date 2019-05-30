<?php
// Copyright 2019 Sergio Guzman sergio@sergioguzman.com
//
// This software uses firmador to sign XML from a HTTP server.
//
// You can redistribute it and/or modify it under the terms of the
// GNU Affero General Public License as published by the Free Software Foundation, 
// either version 3 of the License, or (at your option) any later version, and also under
// the terms of xmlseclibs licensing.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @author    2019 Sergio Guzman <sergio@sergioguzman.com>
require(dirname(__FILE__) . '/hacienda/firmador.php');

use Hacienda\Firmador;
$firmador = new Firmador();

// Metodo de uso: hacer un POST al archivo signer.php
// el XML debe ir en el body ya sea en BASE64 encoded o no
// debe enviar el nombre del archivo del certificado el pin del mismo en el Request URI
// ejemplo:
//
// POST http://127.0.0.1:8080/signer.php?cert=certificado.p12&pin=1111
//   en el body del post debe ir el XML
// 
// -- Nota previo a utilizar este archivo se debe haber hecho un POST a cert-upload.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$urlArr = parse_url($_SERVER['REQUEST_URI']);
	parse_str($urlArr['query'], $vars);

	$pfx    = '/tmp/certs/' . $vars['cert'] ; // Ruta del archivo de la llave criptográfica (*.p12)

	if (! file_exists($pfx)) { # The certificate couldn't be found in /tmp/certs
		http_response_code(412); // Precondition Failed
		print "The certificate is not found here, please send the certificate and try again.";
		exit();
	}

	$pin    = $vars['pin']; // PIN de 4 dígitos de la llave criptográfica
	if (empty($pin) or empty($pfx)) {
		http_response_code(400);  // bad request
		print "Incomplete request";
		exit();
	}
	if (strlen($pin) < 4) {
		http_response_code(400);  // bad request
		print "Pin length must be greater than or equal to 4.";
		exit();
	}

	$xml = file_get_contents('php://input'); // XML posted in body

	$is_base64 = false;
	if (!preg_match('%\<?xml%', $xml)) { // very simple detection
		$is_base64 = true;
		$xml = base64_decode($xml);
	}

	if (!preg_match('%\<?xml%', $xml)) {
		http_response_code(400);  // bad request
		print "File does not appear to be a XML\n";
		exit();
	}

	$base64 = $firmador->firmarXml($pfx, $pin, $xml, $firmador::TO_BASE64_STRING);
	http_response_code(200);
	print_r($base64);
} else {
	http_response_code(405); # Method not allowed
	print "Method not allowed";
}

