<?php
require '../conexionPDO.php';
require_once 'stock_class.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idproveedor = $_POST['idproveedor'];
    $descripciones = $_POST['descripcion_art'];
    $tipos = $_POST['tipo_stock'];

    $articulos = [];

    for ($i = 0; $i < count($descripciones); $i++) {
        $stock = new Stock($base);
        $stock->setDescArt($descripciones[$i]);
        $stock->setTipoStock($tipos[$i]);
        $articulos[] = $stock;
    }

    $stockModel = new Stock($base);
    $resultado = $stockModel->altaArticulos($articulos, $idproveedor);

    if ($resultado === true) {
        echo "<script>alert('Pedido guardado con éxito.'); window.location.href = 'inicioStock.php';</script>";
    } else {
        echo "<script>alert('Error: Datos inconsistentes.'); window.history.back();</script>";
        exit;
    }
} else {
    echo "Acceso no permitido.";
}
?>
