<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliente Nuevo</title>
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
    include("clientes_class.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dni = $_POST['dni'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $email = $_POST['email'];
        $estado = 1;
        
        if (!is_numeric($dni) || strlen($dni) !== 8) {
            echo "<h2 style='text-align:center;'>Error: El DNI debe ser numérico y contener exactamente 8 dígitos.</h2>";
        }else{
            
            // llamamos a la clase clientes
            $cliente = new Clientes($base);
            
            //enviamos los datos a la clase mediante set
            $cliente->setDniCli($dni);
            $cliente->setNomCli($nombre);
            $cliente->setApeCli($apellido);
            $cliente->setTelCli($telefono);
            $cliente->setDirCli($direccion);
            $cliente->setEmailCli($email);
            $cliente->setEstadoCli($estado);

            if ($cliente->altaCli()) {
                echo "<h2 style='text-align:center;'>¡Se ha agregado al cliente con éxito!</h2>";
            } else {
                echo "<h2 style='text-align:center;'>Ups! Error al agregar al cliente.</h2>";
            }
        }
        
    }
    echo "<div class='button-group' style='margin-top:50px;'>";
    echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='inicioClientes.php'>Ver Clientes</a></button>";
    echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='../electrodomesticos/altaElectro.php?dni=$dni'>Agregar Reparación</a></button>";
    echo "</div>";

?>