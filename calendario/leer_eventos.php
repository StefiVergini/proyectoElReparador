<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../conexionPDO.php");
session_start(); 

// actualizar eventos vencidos (estado_evento a 0 y color gris si han pasado 5 días y no se ha modificado)
$hoy = new DateTime();
$sql_actualizar = "UPDATE calendario SET estado_evento = 0 WHERE fecha_fin <= DATE_SUB(NOW(), INTERVAL 5 DAY)";
$base->exec($sql_actualizar);


// Consulta para obtener hasta 7 eventos que no hayan finalizado
$sql = "SELECT * FROM calendario WHERE idempleados = :id ORDER BY fecha_fin ASC LIMIT 7;";
$resultado = $base->prepare($sql);
$id = $_SESSION["id"];


try {
    $resultado->bindValue(':id', $id, PDO::PARAM_INT);
    $resultado->execute();
    
    $eventos = $resultado->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si se obtienen eventos
    if (!$eventos) {
        echo json_encode(["error" => "No se encontraron eventos."]);
        exit;
    }

    $eventos_coloreados = array_map(function($evento) use ($hoy) {
        $estado = $evento['estado_evento'];
        $fecha_fin = new DateTime($evento['fecha_fin']);
        $diferenciaHoras = ($fecha_fin->getTimestamp() - $hoy->getTimestamp()) / 3600;

        // Asignar color según el tiempo restante o estado
        if ($estado == 0) {
            $evento['color'] = '#494949'; // gris para eventos finalizados #808080
        } elseif ($diferenciaHoras <= 72) {
            $evento['color'] = '#8c3939c3'; // rojo para <= 72 horas #ffcccc
        } elseif ($diferenciaHoras <= 120) {
            $evento['color'] = '#d1d10dc2'; // amarillo para entre 72 y 120 horas #ffffcc
        } else {
            $evento['color'] = '#105810c9'; // verde para > 120 horas #ccffcc
        }
        return $evento;
    }, $eventos);
    
    // Devolver los eventos coloreados en formato JSON
    echo json_encode($eventos_coloreados);
} catch (PDOException $e) {
    // Enviar el error en formato JSON
    echo json_encode(["error" => $e->getMessage()]);
}
?>
