<?php
// servicio/server.php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../models/Equipo.php';
require_once __DIR__.'/../models/Solicitante.php';
require_once __DIR__.'/../models/Prestamo.php';
require_once __DIR__.'/../services/EquipoService.php';
require_once __DIR__.'/../services/PrestamoService.php';
require_once __DIR__.'/../services/AuthService.php';

$server = new soap_server();
$server->configureWSDL('PrestamoEquipos', 'urn:PrestamoEquipos');

// Registro de operaciones y tipos complejos...
$ops = [
    'login'              => ['username' => 'xsd:string', 'password' => 'xsd:string'],
    'registrarEquipo'    => ['codigo' => 'xsd:string', 'nombre' => 'xsd:string', 'tipo' => 'xsd:string', 'estado' => 'xsd:string'],
    'consultarEquipo'    => ['id' => 'xsd:int'],
    'listarEquipos'      => [],
    'actualizarEquipo'   => ['id' => 'xsd:int', 'nombre' => 'xsd:string', 'tipo' => 'xsd:string', 'estado' => 'xsd:string'],
    'registrarPrestamo'  => ['equipoId' => 'xsd:int', 'solicitanteId' => 'xsd:int', 'fechaPrestamo' => 'xsd:string', 'fechaEntrega' => 'xsd:string'],
    'consultarPrestamos' => [],
];

$server->wsdl->addComplexType(
    'RespuestaLogin',
    'complexType',
    'struct',
    'all',
    '',
    [
        'token'  => ['name' => 'token',  'type' => 'xsd:string'],
        'expira' => ['name' => 'expira', 'type' => 'xsd:string'],
        'nombre' => ['name' => 'nombre', 'type' => 'xsd:string']
    ]
);

$server->wsdl->addComplexType(
    'RespuestaSOAP',
    'complexType',
    'struct',
    'all',
    '',
    [
        'exito'   => ['name' => 'exito',   'type' => 'xsd:boolean'],
        'mensaje' => ['name' => 'mensaje', 'type' => 'xsd:string'],
        'datos'   => ['name' => 'datos',   'type' => 'tns:RespuestaLogin']
    ]
);

foreach ($ops as $name => $params) {
    $server->register(
        $name,
        $params,
        ['return' => 'tns:RespuestaSOAP'],
        'urn:PrestamoEquipos',
        'urn:PrestamoEquipos#'.$name,
        'rpc',
        'encoded',
        $name
    );
}

$db   = obtenerConexion();
$auth = new AuthService($db);
$es   = new EquipoService($db, new Equipo($db));
$ps   = new PrestamoService($db, new Equipo($db), new Solicitante($db), new Prestamo($db));

function xml(array $x): array {
    return [
        'exito'   => (bool)$x['exito'],
        'mensaje' => (string)$x['mensaje'],
        'datos'   => $x['datos'] ?? []
    ];
}

function login($username, $password) { global $auth; return xml($auth->login($username, $password)); }
function registrarEquipo($codigo, $nombre, $tipo, $estado) { global $es; return xml($es->registrar($codigo, $nombre, $tipo, $estado)); }
function consultarEquipo($id) { global $es; return xml($es->consultar($id)); }
function listarEquipos() { global $es; return xml($es->listar()); }
function actualizarEquipo($id, $nombre, $tipo, $estado) { global $es; return xml($es->actualizar($id, $nombre, $tipo, $estado)); }
function registrarPrestamo($equipoId, $solicitanteId, $fechaPrestamo, $fechaEntrega) { global $ps; return xml($ps->registrar($equipoId, $solicitanteId, $fechaPrestamo, $fechaEntrega)); }
function consultarPrestamos() { global $ps; return xml($ps->consultar()); }

$HTTP_RAW_POST_DATA = file_get_contents('php://input');

// Validar WS-Security en peticiones POST (excepto 'login')
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $esPeticionLogin = str_contains($HTTP_RAW_POST_DATA, 'login');

    if (!$esPeticionLogin && !$auth->validarWssSecurity($HTTP_RAW_POST_DATA)) {
        header('HTTP/1.1 401 Unauthorized');
        header('Content-Type: text/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>
        <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
            <SOAP-ENV:Body>
                <SOAP-ENV:Fault>
                    <faultcode>SOAP-ENV:Client.Authentication</faultcode>
                    <faultstring>Acceso denegado: Cabecera WS-Security inválida o token expirado.</faultstring>
                </SOAP-ENV:Fault>
            </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>';
        exit;
    }
}

$server->service($HTTP_RAW_POST_DATA);