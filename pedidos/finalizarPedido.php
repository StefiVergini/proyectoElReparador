<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("../proveedores/proveedores_class.php");
    
    $proveedores = new Proveedores($base);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_ped = $_POST['id_ped'] ?? null;
        $fecha_fin = $_POST['fecha_fin'] ?? null;
        $cant_ingresa = $_POST['cant_ingresa'] ?? [];
        $fecha_pedido = $_POST['fecha_pedido'];
        $hoy = date('Y-m-d');
        if ($fecha_fin < $fecha_pedido) {
            echo "<script>alert('Error, la fecha de ingreso no puede ser inferior a la del Pedido'); window.location.href = 'pedidosActivos.php';</script>";
        }
        
        if ($fecha_fin > $hoy) {
            echo "<script>alert('Error. La fecha de finalización no puede ser posterior a hoy ($hoy)'); window.location.href = 'pedidosActivos.php';</script>";
        }

        if($pedidos = $proveedores->finalizarPedido($id_ped,$fecha_fin, $cant_ingresa)){
            echo "<script>alert('Pedido Finalizado con Éxito!'); window.location.href = 'historialPedidos.php';</script>";
        }
        
    }
?>