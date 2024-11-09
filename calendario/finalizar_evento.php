<?php
include("../conexionPDO.php");

try {
    $data = json_decode(file_get_contents("php://input"));
    $id_evento = $data->id;
    $fecha_fin = $data->fecha_fin;

    // Prepara y ejecuta la consulta para finalizar el evento
    $sql = "UPDATE calendario SET fecha_fin = :fecha_fin, estado_evento = 0 WHERE idcalendario = :id_evento";
    $stmt = $base->prepare($sql);
    $stmt->bindParam(':fecha_fin', $fecha_fin);
    $stmt->bindParam(':id_evento', $id_evento);

    // Ejecuta la consulta
    $success = $stmt->execute();

    // Verifica si la operación fue exitosa
    if ($success) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "No se pudo finalizar el evento."]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
