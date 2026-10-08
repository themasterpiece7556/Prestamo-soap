<?php
// services/AuthService.php

class AuthService {

    public function __construct(private PDO $db) {}

    public function login(string $username, string $password): array {
        $username = trim($username);
        $password = trim($password);

        $stmt = $this->db->prepare('SELECT id, nombre, password_hash FROM solicitantes WHERE username = ?');
        $stmt->execute([$username]);
        $solicitante = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$solicitante || !password_verify($password, $solicitante['password_hash'])) {
            return ['exito' => false, 'mensaje' => 'Credenciales inválidas', 'datos' => null];
        }

        $token = bin2hex(random_bytes(32));
        $expiracion = date('Y-m-d H:i:s', strtotime('+2 hours'));

        $update = $this->db->prepare('UPDATE solicitantes SET token_acceso = ?, token_expiracion = ? WHERE id = ?');
        $update->execute([$token, $expiracion, $solicitante['id']]);

        return [
            'exito' => true,
            'mensaje' => 'Autenticacion exitosa',
            'datos' => [
                'token' => $token,
                'expira' => $expiracion,
                'nombre' => $solicitante['nombre']
            ]
        ];
    }

    /**
     * Valida el encabezado estándar WS-Security (UsernameToken)
     */
    public function validarWssSecurity(string $xmlInput): bool {
        if (empty($xmlInput)) {
            return false;
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        if (!$dom->loadXML($xmlInput)) {
            libxml_clear_errors();
            return false;
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('wsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');

        $userNodes = $xpath->query('//wsse:Security/wsse:UsernameToken/wsse:Username');
        $passNodes = $xpath->query('//wsse:Security/wsse:UsernameToken/wsse:Password');

        if ($userNodes->length === 0 || $passNodes->length === 0) {
            return false;
        }

        $username = trim($userNodes->item(0)->nodeValue);
        $token = trim($passNodes->item(0)->nodeValue);

        // Validar contra la BD que el usuario corresponda al token y este no haya expirado
        $stmt = $this->db->prepare(
            'SELECT id FROM solicitantes 
             WHERE username = :username AND token_acceso = :token AND token_expiracion > NOW()'
        );
        $stmt->execute([
            ':username' => $username,
            ':token'    => $token
        ]);
        
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }
}