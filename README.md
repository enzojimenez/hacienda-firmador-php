# Firmador PHP - MdH

Clase PHP para firmar comprobantes electrónicos para el Ministerio de Hacienda de Costa Rica

## Instalación

Requerido: PHP version 5.6.24+ recomendado por razones de seguridad

```bash
git clone https://github.com/enzojimenez/hacienda-firmador-php.git
```

## Uso

### FIRMAR:

```php
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
```

### VALIDAR:

[Próximanente!...]

## Quiere contribuir?
Los "Pull Requests" son bienvenidos.
Para cambios importantes, primero abra un "Issue" para discutir qué le gustaría cambiar o mejorar.

## Licencia
[GNU AGPL](http://www.gnu.org/licenses/)
