<?php
include("../header.php");
include("../conexionPDO.php");
include("stock_class.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $idstock = $_POST["id"];
    $stockModel = new Stock($base);

    if ($stockModel->bajaArticulo($idstock)) {
        echo "<script>alert('Artículo eliminado con éxito.'); window.location.href = 'inicioStock.php';</script>";
    } else {
        echo "<script>alert('No se pudo eliminar el artículo. Tiene stock disponible o tiene un Pedido Activo.'); window.location.href = 'inicioStock.php';</script>";
    }
} else {
    echo "<script>alert('Solicitud no válida.'); window.location.href = 'inicioStock.php';</script>";
}
?>
