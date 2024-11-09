<?php
require '../conexionPDO.php';
$error = '';
$success_message = '';
$idclientes = $_POST['cliente_id'] ?? '';
$idtipo_electro = $_POST['idtipo_electro'] ?? '';
$marca = $_POST['marca'] ?? '';
$modelo = $_POST['modelo'] ?? '';
$nro_serie = $_POST['nro_serie'] ?? '';
$color = $_POST['color'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$idempleados = $_POST['idempleados'] ?? '';
try {
    $sql_insert_electro = "INSERT INTO electrodomesticos (marca, modelo, num_serie, descripcion, idclientes, tipo_electro) 
        VALUES (:marca, :modelo, :nro_serie, :descripcion, :idclientes, :idtipo_electro)";

    $stmt_insert_electro = $base->prepare($sql_insert_electro);
    $stmt_insert_electro->execute([
        'marca' => $marca,
        'modelo' => $modelo,
        'nro_serie' => $nro_serie,
        'descripcion' => $descripcion,
        'idclientes' => $idclientes,
        'idtipo_electro' => $idtipo_electro
    ]);
    $idelectrodomestico = $base->lastInsertId();
   $sql_reparacion = "INSERT INTO reparaciones (idelectrodomesticos, idempleados, fecha_inicio, fecha_fin_estimada) 
        VALUES (:idelectrodomestico, :idempleados, NOW(), DATE_ADD(NOW(), INTERVAL 5 DAY))";

    $stmt_reparacion = $base->prepare($sql_reparacion);
    $stmt_reparacion->execute([
        'idelectrodomestico' => $idelectrodomestico,
        'idempleados' => $idempleados,
    ]);
    $success_message = "<script>alert('Reparación agregada correctamente.');</script>";
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}
if ($error) {
    echo $error;
} else {    
    echo "<script>alert('Reparación agregada correctamente.');
    window.location.href = './electrodomesticos.php';</script>"; 
    
}
