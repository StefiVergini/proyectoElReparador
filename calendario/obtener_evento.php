<?php
include("../conexionPDO.php");

try {
    $id_evento = $_GET['id'];

    // Prepara y ejecuta la consulta para obtener el evento
    $sql = "SELECT * FROM calendario WHERE idcalendario = :id_evento";
    $stmt = $base->prepare($sql);
    $stmt->bindParam(':id_evento', $id_evento);
    $stmt->execute();

    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($evento) {
        echo json_encode($evento);
    } else {
        echo json_encode(["error" => "Evento no encontrado."]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
