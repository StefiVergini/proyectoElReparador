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
    include("proveedores_class.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $cuit = $_POST['cuit'];
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $email = $_POST['email'];
        $saldo = $_POST['saldo'];
        
        if (!is_numeric($cuit) || strlen($cuit) !== 11) {
            echo "<h2 style='text-align:center;'>Error: El CUIT debe ser numérico y contener exactamente 11 dígitos.</h2>";
            return false;
        }else{
            
            //cambiamos a string el cuit
            $cuit = strval($cuit);

            // llamamos a la clase proveedores
            $proveedor = new Proveedores($base);
            
            //enviamos los datos a la clase mediante set
            $proveedor->setIdProv($id);
            $proveedor->setCuit($cuit);
            $proveedor->setNomProv($nombre);
            $proveedor->setTelProv($telefono);
            $proveedor->setDirProv($direccion);
            $proveedor->setEmailProv($email);
            $proveedor->setSaldo($saldo);

            // Generar la modificacion
            if ($proveedor->modificarProv()) {
                echo "<h2 style='text-align:center;'>¡Proveedor modificado exitosamente!</h2>";
            } else {
                echo "<h2 style='text-align:center;'>Error al modificar el proveedor.</h2>";
            }
        }
    }
    echo "<div class='button-group' style='margin-top:50px;'>";
    echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='inicioProv.php'>Proveedores</a></button>";
    echo "</div>";
?>