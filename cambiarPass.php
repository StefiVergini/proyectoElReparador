<?php
    require './conexionPDO.php';
    session_start();
    $id = $_SESSION["id"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recibimos y sanitizamos las contraseñas
        $password = isset($_POST['password']) ? htmlentities(addslashes($_POST["password"])) : '';
        $cpassword = isset($_POST['cpassword']) ? htmlentities(addslashes($_POST['cpassword'])) : '';

        // Verificamos que ambas contraseñas coincidan
        if ($password!== $cpassword) {
            echo "<p style='display: inline-flex;justify-content: center;align-items: center; color: red; font-weight:bold; background-color:black; text-align:center;'>Las contraseñas no coinciden.</p>";
        } elseif ($password != '' && $cpassword !='')
           if(strlen($password) > 8 || strlen($password) < 4 || !preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$/', $password)) {
                echo "<p style='display: inline-flex;justify-content: center;align-items: center; color: red; font-weight:bold; background-color:black; text-align:center;'>La contraseña debe tener un mínimo 4 caracteres y máximo de 8.</p>";
                echo "<p style='display: inline-flex;justify-content: center;align-items: center; color: red; font-weight:bold;background-color:black; text-align:center;'>Debe contener letras y números.</p>";
        } else {
            // Ciframos la contraseña
            $pass_cifrado = password_hash($password, PASSWORD_DEFAULT, array("cost" => 8));
            
            // Realizamos el update en la base de datos
            $query = "UPDATE credenciales SET password = :password WHERE idempleados = :id";
            $resultado = $base->prepare($query);
            $resultado->bindParam(':password', $pass_cifrado);
            $resultado->bindParam(':id', $id);
            
            if ($resultado->execute()) {
                echo "<script> alert('Contraseña Actualizada con Éxito!');
                window.location.href = ' /php/proyectoElReparador/electrodomesticos/electrodomesticos.php';</script>";
                exit();
            } else {
                echo "<script> alert('Error al Actualizar la Contraseña.');
                window.location.href = ' /php/proyectoElReparador/login/index.php';</script>";
                exit();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./static/styles/grid.css" />

    <title>Cambiar Contraseña</title>
</head>
<body>
    <div class="cont">
        <button class="button" style="background-color:gray;" type="submit"><a style= "text-decoration:none; color:white;"href="/php/proyectoElReparador/electrodomesticos/electrodomesticos.php">Volver</a></button>
    </div>
    <div class="container">

        <h1>Modificar Contraseña</h1>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input class="input" type="email" id="email" name="email" hidden>
            <div class="datos">
                <label for="password">Nueva Contraseña</label>
                <input class="input" type="password" id="password" name="password" placeholder="**********" required>
            </div>
            <div class="datos">
                <label for="cpassword">Repita Contraseña</label>
                <input class="input" type="password" id="cpassword" name="cpassword" placeholder="**********" required>
            </div>
            <div class="cont">
                <button class="button" type="submit">Cambiar</button>
            </div>
        </form>
    </div>

</body>
</html>