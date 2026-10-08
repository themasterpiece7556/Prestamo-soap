<?php
// Cargar la conexión a la base de datos
require_once __DIR__ . '/config/database.php';

try {
    $db = obtenerConexion();

    // Generar un hash real y válido con el algoritmo actual de PHP
    $passHash = password_hash('123456', PASSWORD_BCRYPT);

    // Actualizar la contraseña para el usuario juan_perez
    $stmt = $db->prepare("UPDATE solicitantes SET password_hash = ? WHERE username = 'juan_perez'");
    $stmt->execute([$passHash]);

    echo "<h1>¡Éxito!</h1>";
    echo "<p>El hash para 'juan_perez' fue actualizado correctamente.</p>";
    echo "<p><strong>Nuevo hash generado:</strong> " . htmlspecialchars($passHash) . "</p>";

} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}