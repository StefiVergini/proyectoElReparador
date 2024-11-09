<?php
include("../conexionPDO.php");

header('Content-Type: application/json');

try {
    // Obtener los datos de la solicitud POST
    $data = json_decode(file_get_contents("php://input"), true);
    $id_evento = $data['id_evento'] ?? null;
    $fecha_fin = date("Y-m-d");
    $hora_fin = date("H:i:s");

    if ($id_evento === null) {
        echo json_encode(["success" => false, "error" => "ID de evento no proporcionado."]);
        exit;
    }

    // Prepara y ejecuta la consulta para actualizar el estado y fechas del evento
    $sql = "UPDATE calendario SET fecha_fin = :fecha_fin, hora_fin = :hora_fin, estado_evento = 0 WHERE idcalendario = :id_evento";
    $stmt = $base->prepare($sql);
    $stmt->bindParam(':fecha_fin', $fecha_fin);
    $stmt->bindParam(':hora_fin', $hora_fin);
    $stmt->bindParam(':id_evento', $id_evento, PDO::PARAM_INT);
    
    $success = $stmt->execute();

    if ($success) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "No se pudo borrar el evento."]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>