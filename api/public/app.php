<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Hacienda\Firmador;

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../hacienda/firmador.php';

$app = AppFactory::create();

$app->post('/sign', function (Request $request, Response $response, array $args) {
    try {
        $params = $request->getParsedBody();
        $pfx = $params["pfx"];
        $pin = $params["pin"];
        $xml = base64_decode($params["xml"]);

        $firmador = new Firmador();
        $base64 = $firmador->firmarXml($pfx, $pin, $xml, $firmador::TO_BASE64_STRING);

        $response->getBody()->write(json_encode([
            "status" => json_last_error_msg(),
            "xml" => $base64
        ]));
    } catch (Exception $ex) {
        $response->getBody()->write(json_encode([
            "status" => $ex->getMessage(),
            "xml" => null
        ]));
    }
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();