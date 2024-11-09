<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baja Proveedor</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>

<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("proveedores_class.php");

    if (isset($_POST['id'])) {
        $id =  htmlentities(addslashes($_POST['id']));

        $proveedoresModel = new Proveedores($base);

        $resultado = $proveedoresModel->bajaProveedor($id);

        if ($resultado) {
            echo "<h2 style='text-align:center;'>El proveedor ha sido dado de baja correctamente.</h2>";
        } else {
            echo "<h2 style='text-align:center;'>Error al dar de baja al proveedor.</h2>";
        }
    } else {
        echo "<h2 style='text-align:center;'>Ups! Ocurrió un error imprevisto.</h2>";
    }

    echo "<div class='button-group' style='margin-top:50px;'>";
    echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='inicioProv.php'>Proveedores</a></button>";
    echo "</div>";

?>
</body>
</html>
