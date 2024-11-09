<?php
session_start();

try {
    require '../conexionPDO.php';
    include("../static/styles/grid.css");

    $correo = isset($_POST["email"]) ? htmlentities(addslashes($_POST["email"])) : '';
    $contrasenia = isset($_POST["password"]) ? htmlentities(addslashes($_POST["password"])) : '';
    $contrasenia_correcta = false;

    $sql = "SELECT e.email_empleado, c.password, c.usuario, e.idempleados, h.idcategorias_empleados 
            FROM empleados AS e 
            INNER JOIN credenciales AS c ON e.idempleados = c.idempleados 
            INNER JOIN historial_empleados AS h ON e.idempleados = h.idempleados
            WHERE e.email_empleado = :email";

    $resultado = $base->prepare($sql);
    $resultado->execute(array("email" => $correo));

    while ($registro = $resultado->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($contrasenia, $registro["password"])) {
            $contrasenia_correcta = true;
            $_SESSION['usuario'] = $registro['usuario'];
            $_SESSION['id'] = $registro['idempleados'];
            $_SESSION['rol'] = $registro['idcategorias_empleados'];

            if($contrasenia == '1234'){
                echo "<script> alert('Usted Tiene una Contraseña Genérica - Debe Modificarla');
                window.location.href = ' /php/proyectoElReparador/cambiarPass.php';</script>";
                exit();
            }else{
                header('location: /php/proyectoElReparador/electrodomesticos/electrodomesticos.php');
                exit();
            }
        }
    }
    
    if (!$contrasenia_correcta) {
        $_SESSION['error'] = "Email o contraseña incorrectos. Por favor intenta de nuevo.";
        header('Location: /php/proyectoElReparador/login/index.php');
        exit();
    } 

    $resultado->closeCursor();
} catch (Exception $e) {
    die("error: " . $e->getMessage());
}
?>