<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    
</body>
</html>
<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("stock_class.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $cant = $_POST['cant'];
        $tipo = $_POST['tipoStock'];
        $idprov = $_POST['idproveedor'];

        $articulo = new Stock($base);
            
        $articulo->setIdStock($id);
        $articulo->setDescArt($nombre);
        $articulo->setCantidad($cant);
        $articulo->setTipoStock($tipo);
        $articulo->setIdProv($idprov);
            // Generar la modificacion
            if ($articulo->modificarArt()) {
                echo "<script>alert('Artículo modificado con éxito.'); window.location.href = 'inicioStock.php';</script>";
            } else {
                echo "<h2 style='text-align:center;'>Error al modificar el Articulo.</h2>";
            }
        
    }
    echo "<div class='button-group' style='margin-top:50px;'>";
    echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='inicioStock.php'>Artículos | Stock</a></button>";
    echo "</div>";
?>