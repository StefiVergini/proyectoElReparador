<?php
include "../conexionPDO.php";

// Utiliza $_POST en lugar de obtener los datos como JSON
$id_evento = $_POST['idcalendario'] ?? null; // usar idcalendario
$descripcion_evento = $_POST['inputDescripcion'] ?? null; // usar inputDescripcion
$fecha_inicio = $_POST['inputFechaInicio'] ?? null; // usar inputFechaInicio
$hora_inicio = $_POST['inputHoraInicio'] ?? null; // usar inputHoraInicio
$fecha_fin = $_POST['inputFechaFin'] ?? null; // usar inputFechaFin
$hora_fin = $_POST['inputHoraFin'] ?? null; // usar inputHoraFin

// Verificar que todos los campos son válidos
if (!$id_evento || !$descripcion_evento || !$fecha_inicio || !$hora_inicio || !$fecha_fin || !$hora_fin) {
    echo json_encode(["success" => false, "error" => "No se recibieron datos válidos o faltan parámetros."]);
    exit;
}

try {
    if ($fecha_fin < $fecha_inicio ) {
        echo "<script>alert('Error! La Fecha de Finalización no puede ser anterior a la Fecha de Inicio');window.location.href = 'inicioCalendario.php';</script>";
        exit;
    }else{
        $sql = "UPDATE calendario 
            SET descripcion_evento = :descripcion_evento, 
                fecha_inicio = :fecha_inicio, 
                hora_inicio = :hora_inicio, 
                fecha_fin = :fecha_fin, 
                hora_fin = :hora_fin 
            WHERE idcalendario = :id_evento";

        $stmt = $base->prepare($sql);
        $stmt->bindParam(':descripcion_evento', $descripcion_evento);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->bindParam(':hora_fin', $hora_fin);
        $stmt->bindParam(':id_evento', $id_evento);
        $success = $stmt->execute();
        if($success){
            echo "<script>alert('Se han modificado los datos con éxito!');
                        window.location.href = './inicioCalendario.php';
                </script>";
        }else{
            echo "<script>alert('Hubo un error en la modificación');
                        window.location.href = './inicioCalendario.php';
                </script>";
        }
        //echo json_encode(["success" => $success]);
        exit;
    }
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}