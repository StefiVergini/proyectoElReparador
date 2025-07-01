<?php

class Notificacion {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Obtener notificaciones sin leer para un usuario
    public function obtenerNoLeidas($userId) {
        $stmt = $this->db->prepare("SELECT * FROM notificaciones WHERE id_usuario = ? AND fue_leido = 0 ORDER BY fecha_creado DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Marcar una notificación (o un grupo) como leída
    public function marcarComoLeida($notificacionId) {
        $stmt = $this->db->prepare("UPDATE notificaciones SET fue_leido = 1 WHERE id = ?");
        return $stmt->execute([$notificacionId]);
    }

    // Crear una notificación para un usuario
    public function crearNoti($userId, $mensaje, $link) {
        $stmt = $this->db->prepare("INSERT INTO notificaciones (id_usuario, mensaje, link) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $mensaje, $link]);
    }
}

?>