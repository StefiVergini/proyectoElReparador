<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volver a dar de Alta Cliente</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    
    
    <?php
    
    include("../header.php");
    include("../conexionPDO.php");
    include("clientes_class.php");

    if (isset($_POST['id'])) {
        $id =  htmlentities(addslashes($_POST['id']));

        $clientesModel = new Clientes($base);

        $resultado = $clientesModel->actualizarCliente($id);

        if ($resultado) {
            echo "<h2 style='text-align:center;'>¡Se ha dado de alta nuevamente al cliente con éxito!</h2>";
        } else {
            echo "<h2 style='text-align:center;'>Error al volver a dar de Alta al cliente.</h2>";
        }
    } else {
        echo "<h2 style='text-align:center;'>Ups! Ocurrió un error imprevisto.</h2>";
    }
    echo "<div class='button-group' style='margin-top:50px;'>";
    echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='inicioClientes.php'>Clientes</a></button>";
    echo "</div>";



    ?>

</body>
</html>