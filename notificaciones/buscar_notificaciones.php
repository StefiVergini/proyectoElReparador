<?php
// buscar_notificaciones.php
require_once __DIR__ . '/../conexionPDO.php';
require_once __DIR__ . '/notificaciones_class.php';
session_start();

if (!isset($_SESSION['id'])) {
    echo json_encode([
        'unread' => [],
        'read'   => [],
        'count'  => 0
    ]);
    exit;
}

$userId = $_SESSION['id'];
$notificacion = new Notificacion($base);

// Obtener las notificaciones no leídas
$unread = $notificacion->obtenerNoLeidas($userId);

// Obtener las notificaciones leídas (por ejemplo, de los últimos dos días)
$stmt = $base->prepare("SELECT * FROM notificaciones WHERE id_usuario = ? AND fue_leido = 1 AND fecha_creado >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY fecha_creado DESC");
$stmt->execute([$userId]);
$read = $stmt->fetchAll(PDO::FETCH_ASSOC);

// El contador se basará en las no leídas
$count = count($unread);

echo json_encode([
    'unread' => $unread,
    'read'   => $read,
    'count'  => $count
]);
exit;
?>
