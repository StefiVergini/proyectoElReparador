<?php
require './conexionPDO.php';
if (isset($_POST['nombre'], $_POST['apellido'], $_POST['telefono'], $_POST['direccion'])) {

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    try {
        $sql = "UPDATE empleados SET nom_empleado = :nombre, ape_empleado = :apellido, tel_empleado = :telefono, dir_empleado = :direccion WHERE email_empleado = :email";
        $stmt = $base->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':email', $usuario);
        $stmt->execute();
        $success_message = 'Datos actualizados correctamente.';
    } catch (Exception $e) {
        $error = 'Error al actualizar los datos: ' . $e->getMessage();
    }
}
