<?php
// middleware/AuthMiddleware.php

function validarTokenRequest() {
    // Extraer token del encabezado 'Authorization: Bearer <token>'
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        throw new Exception("Acceso no autorizado: Token no proporcionado.");
    }

    $token = $matches[1];
    $db = Database::getConnection();

    // Consultar token válido y no expirado
    $stmt = $db->prepare("SELECT id, nombre FROM solicitantes WHERE token_acceso = :token AND token_expiracion > NOW()");
    $stmt->execute([':token' => $token]);
    $solicitante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$solicitante) {
        throw new Exception("Acceso no autorizado: Token inválido o expirado.");
    }

    return $solicitante; // Retorna el usuario autenticado
}