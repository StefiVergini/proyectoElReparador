<?php
include("../conexionPDO.php");

//agregar evento
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hora_ini = $_POST['hora_ini'];
    $fecha_ini = $_POST['fecha_ini'];
    $descripcion = $_POST['descripcion'];
    $hora_fin = $_POST['hora_fin'];
    $fecha_fin = $_POST['fecha_fin'];
    $id = 1;
    $estado = 1;
    $fecha_inicio_mysql = date('Y-m-d', strtotime($fecha_ini));
    $fecha_fin_mysql = date('Y-m-d', strtotime($fecha_fin));
    $hora_ini_mysql = date('H:i', strtotime($hora_ini));
    $hora_fin_mysql = date('H:i', strtotime($hora_fin));

    if ($fecha_fin_mysql < $fecha_inicio_mysql ) {
        echo "<script>alert('Error! La Fecha de Finalización no puede ser anterior a la Fecha de Inicio');window.location.href = 'inicioCalendario.php';</script>";
        exit;
    }else{
        $sql = "INSERT INTO calendario (descripcion_evento, fecha_inicio, hora_inicio, hora_fin, fecha_fin, estado_evento, idempleados) VALUES (:descripcion, :fecha_ini, :hora_ini, :hora_fin, :fecha_fin, :estado, :id)";

        $stmt = $base->prepare($sql);
        $stmt->bindParam(':hora_ini', $hora_ini_mysql);
        $stmt->bindParam(':fecha_ini', $fecha_inicio_mysql);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':hora_fin', $hora_fin_mysql);
        $stmt->bindParam(':fecha_fin', $fecha_fin_mysql);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
    
        if ($stmt->execute()) {
            echo "<script>alert('Evento agregado exitosamente'); window.location.href = 'inicioCalendario.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error al agregar el evento');</script>";
        }
    }


}
?>