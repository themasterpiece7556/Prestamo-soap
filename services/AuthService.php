<?php
// controllers/AuthController.php

function login($username, $password) {
    $db = Database::getConnection();
    
    // 1. Buscar usuario
    $stmt = $db->prepare("SELECT id, password_hash FROM solicitantes WHERE username = :user");
    $stmt->execute([':user' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Verificar contraseña
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['status' => 'error', 'message' => 'Credenciales inválidas'];
    }

    // 3. Generar token único (64 caracteres)
    $token = bin2hex(random_bytes(32));
    $expiracion = date('Y-m-d H:i:s', strtotime('+8 hours'));

    // 4. Guardar token en la BD
    $update = $db->prepare("UPDATE solicitantes SET token_acceso = :token, token_expiracion = :exp WHERE id = :id");
    $update->execute([
        ':token' => $token,
        ':exp'   => $expiracion,
        ':id'    => $user['id']
    ]);

    return [
        'status' => 'success',
        'token'  => $token,
        'expires_at' => $expiracion
    ];
}