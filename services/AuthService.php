<?php
// services/AuthService.php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService {
    private string $secretKey;

    public function __construct(private PDO $db) {
        // Obtenemos la clave secreta compartida (puedes definirla en un archivo de config o variable de entorno)
        $this->secretKey = "ClaveSecretaSuperSeguraParaAmbosServicios2026!";
    }

    /**
     * Valida la cabecera WS-Security decodificando el JWT firmado simétricamente.
     */
    public function validarWssSecurity(string $xmlInput): ?array {
        if (empty($xmlInput)) {
            return null;
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        if (!$dom->loadXML($xmlInput)) {
            libxml_clear_errors();
            return null;
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('wsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');

        $passNodes = $xpath->query('//wsse:Security/wsse:UsernameToken/wsse:Password');
        if ($passNodes->length === 0) {
            return null;
        }

        $jwtToken = trim($passNodes->item(0)->nodeValue);

        try {
            // Decodifica y verifica la firma simétrica del token
            $decoded = JWT::decode($jwtToken, new Key($this->secretKey, 'HS256'));
            
            // Retorna los datos del usuario embebidos en el token (ej: id, username)
            return (array) $decoded;
        } catch (\Exception $e) {
            // Token alterado, expirado o clave incorrecta
            return null;
        }
    }
}