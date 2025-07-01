<?php
require_once __DIR__ . '/../conexionPDO.php';
require_once __DIR__ . '/notificaciones_class.php';
session_start();

if (!isset($_SESSION['id'])) {
    http_response_code(400);
    echo "Sesión no definida.";
    exit;
}

$userId = $_SESSION['id'];
$notificacion = new Notificacion($base);

// Actualiza todas las notificaciones sin leer para el usuario.
$stmt = $base->prepare("UPDATE notificaciones SET fue_leido = 1 WHERE id_usuario = ? AND fue_leido = 0");
$result = $stmt->execute([$userId]);

if($result){
    echo "OK";
} else {
    http_response_code(500);
    echo "Error al actualizar notificaciones";
}
?>