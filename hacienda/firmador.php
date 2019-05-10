<?php
namespace Hacienda;

require(dirname(__FILE__) . '/../xmlseclibs/xmlseclibs.php');

use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

/**
 * 
 * xmlseclibs.php is a library written in PHP for working with XML Encryption and Signatures.
 * 
 * The author of xmlseclibs is Rob Richards. Please see the license for xmlseclibs.
 * 
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 * hacienda.php
 *
 * Copyright 2019 Enzo Jiménez <enzofjh@gmail.com>
 * 
 * This class <Firmador> is an extended modification of xmlseclibs.php that has been improved
 * to be used as free software for signing Electronic Invoices in Costa Rica to comply with
 * government laws and protocols by using PHP as the main programming language.
 * 
 * You can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, 
 * either version 3 of the License, or (at your option) any later version, and also under
 * the terms of xmlseclibs licensing.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    2019 Enzo Jiménez <enzofjh@gmail.com>
 */

class Firmador {
    public function firmarXml($pfx,$pin,$unsigned,$signed){
        // Cargar el XML para ser firmado
        $xml = new \DOMDocument();
        $xml->loadXML(file_get_contents($unsigned));

        // Crear un nuevo objeto de seguridad
        $objSec = new XMLSecurityDSig();

        // Mantener el primer nodo secundario original XML en memoria
        $objSec->xmlFirstChild = $xml->firstChild;

        // Cargar la información del certificado desde el archivo *.p12
        $certInfo = $objSec->loadCertInfo($pfx,$pin);

        // Usar la canonicalización exclusiva de c14n.
        $objSec->setCanonicalMethod($objSec::C14N);

        // Cargar la clave privada del certificado
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array('type' => 'private'));
        $objKey->loadKey($certInfo["privateKey"]);

        // Agregar la clave pública asociada a la firma.
        $objSec->add509Cert($certInfo["publicKey"], true);
        $objSec->appendKeyValue($certInfo);

        // Insertar objeto Xades en la firma.
        $objSec->appendXades($certInfo);

        // Firmar utilizando SHA-256
        // Referencia del documento
        $objSec->addReference($xml,$objSec::SHA256, [ 'http://www.w3.org/2000/09/xmldsig#enveloped-signature' ], [ 'id_ref' => $objSec->reference0Id, 'force_uri' => true ]);

        // Referencia de nodo de información clave
        $objSec->addReference($objSec->getKeyInfoNode(),$objSec::SHA256,null, [ 'id_ref' => $objSec->reference1Id, 'force_uri' => false, 'overwrite' => false ]);

        // Referencia del nodo Xades
        $objSec->addReference($objSec->getXadesNode(),$objSec::SHA256,null, [ 'force_uri' => false, 'overwrite' => false, "type" => "http://uri.etsi.org/01903#SignedProperties" ], [ [ 'qualifiedName' => 'xmlns:xades', 'value' => $objSec::XADES ] ]);

        // Firma el archivo xml
        $objSec->sign($objKey);

        // Adjuntar la firma al xml
        $objSec->appendSignature($xml->documentElement);

        // Guarda el xml firmado
        $xml->save($signed);
    }
}