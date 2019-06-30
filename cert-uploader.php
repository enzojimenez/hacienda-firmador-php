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
$enabled = false;
if (!empty($_ENV['CERTIFICATE_UPLOAD']) and strtolower($_ENV['CERTIFICATE_UPLOAD']) == 'true') {
	$enabled = true;
}

// Metodo de uso: hacer un POST al archivo cert-upload.php
// el certificado debe ir en el body
// debe enviar el nombre del archivo del certificado Request URI
// ejemplo:
//
// POST http://127.0.0.1:8080/cert-uploader.php?cert=certificado.p12
//   en el body del post debe ir el Certificado
//   Nota -- si ya existe el certificado con ese nombre sera reemplazado
//
if (! $enabled) {
	http_response_code(428); // Precondition Required
	echo "The uploading service have not been activated by the administrator,\n";
	echo "container must be started with environment CERTIFICATE_UPLOAD=true for this.\n";
	exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $enabled) {
	$urlArr = parse_url($_SERVER['REQUEST_URI']);
	parse_str($urlArr['query'], $vars);

	$pfx    = '/tmp/certs/' . $vars['cert'] ; // Ruta del archivo de la llave criptográfica (*.p12)
	$certificate = file_get_contents('php://input'); // Certificate posted in body

	http_response_code(501);
	$dst_file = fopen($pfx, 'w');
	if (! $dst_file ) {
		echo "Cannot create destination file";
		die();
	}
	if (fwrite($dst_file, $certificate) === FALSE) {
		echo "Cannot write to file";
		exit();
	}
	fclose($dst_file);

	if (! file_exists($pfx)) { # The certificate couldn't be found in /tmp/certs
		http_response_code(412); // Precondition Failed
		print "The certificate could not be written.";
		exit();
	}

	http_response_code(201);
} else {
	http_response_code(405); # Method not allowed
	print "Method not allowed";
}

