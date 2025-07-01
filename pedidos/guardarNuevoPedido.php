<?php
require '../conexionPDO.php';
require_once '../proveedores/proveedores_class.php';
session_start();

$idEmp= $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idProveedor = $_POST['id_proveedor'] ?? null;
    $idStocks = is_array($_POST['id_stock']) ? $_POST['id_stock'] : [];
    $cantidades = is_array($_POST['cant']) ? $_POST['cant'] : [];


    if (!$idProveedor || empty($idStocks) || empty($cantidades)) {
        die("Error: Datos incompletos");
    }

    $detallesPedido = [];
    foreach ($idStocks as $indice => $idStock) {
        $cantidad = intval($cantidades[$indice]);
        if ($cantidad > 0) {
            $detallesPedido[] = [
                'id_stock' => $idStock,
                'cantidad' => $cantidad
            ];
            
        }
    }
    //var_dump($detallesPedido);

    if (empty($detallesPedido)) {
        echo "<script>alert('Error: No se ingresaron cantidades válidas.'); window.history.back();</script>";
        exit;
    }
    if (count($idStocks) !== count($cantidades)) {
        echo "<script>alert('Error: Datos inconsistentes entre artículos y cantidades.'); window.history.back();</script>";
        exit;
    }

    // Instancia de la clase Proveedores
    $pedido = new Proveedores($base);
    $resultado = $pedido->guardarPedido($idEmp,$idProveedor, $detallesPedido);

    if ($resultado === true) {
        echo "<script>alert('Pedido guardado con éxito.'); window.location.href = 'pedidosActivos.php';</script>";
    } else {
        echo $resultado; // Mensaje de error en caso de fallo
    }
}
?>
