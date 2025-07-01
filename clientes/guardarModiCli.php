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
    include("clientes_class.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $dni = $_POST['dni'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['ape'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $email = $_POST['email'];
        
        if (!is_numeric($dni) || strlen($dni) !== 8) {
            echo "<h2 style='text-align:center;'>Error: El DNI debe ser numérico y contener exactamente 8 dígitos.</h2>";
            return false;
        }else{

            // llamamos a la clase clientes
            $cliente = new Clientes($base);
            
            //enviamos los datos a la clase mediante set
            $cliente->setIdCli($id);
            $cliente->setDniCli($dni);
            $cliente->setNomCli($nombre);
            $cliente->setApeCli($apellido);
            $cliente->setTelCli($telefono);
            $cliente->setDirCli($direccion);
            $cliente->setEmailCli($email);

            // Generar la modificacion
            if ($cliente->modificarCli()) {
                echo "<h2 style='text-align:center;'>Cliente modificado exitosamente!</h2>";
            } else {
                echo "<h2 style='text-align:center;'>Error al modificar el cliente.</h2>";
            }
        }
    }
    echo "<div class='button-group' style='margin-top:50px;'>";
    echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='inicioClientes.php'>Clientes</a></button>";
    echo "</div>";
?>