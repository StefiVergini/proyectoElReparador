<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificación Empleado</title>
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
    include("empleados_class.php");
    include_once("historial_emp_class.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id']; 
        $dni = $_POST['dni'];
        $fecha_inicio = $_POST['fecha_ini'];
        
        $fecha_inicio_mysql = date("Y-m-d", strtotime($fecha_inicio));
        // Validación del DNI
        if (!is_numeric($dni) || strlen($dni) !== 8) {
            echo "<h2>Error: El DNI debe ser numérico y contener exactamente 8 dígitos.</h2>";
            return false;
        } else {
            $empleado = new Empleados($base);
            
            // Setear los datos del empleado
            $empleado->setIdEmp($id);
            $empleado->setDniEmp($dni);
            $empleado->setNomEmp($_POST['nombre']);
            $empleado->setApeEmp($_POST['apellido']);
            $empleado->setTelEmp($_POST['telefono']);
            $empleado->setEmailEmp($_POST['email']);
            $empleado->setDirEmp($_POST['direccion']);
            $empleado->setIdLocal($_POST['id_local']);
            
            
            // Modificar el historial del empleado (categoría, fecha y descripción)
            $historial = new HistorialEmp($base);
            $historial->setIdCategoria($_POST['categoria']);
            $historial->setFechaInicio($fecha_inicio_mysql);
            $historial->setDescripcion($_POST['desc']);
            
            if ($historial->modificarCatFecha($id) && $empleado->modificarEmp()) {
                echo "<h2 style='text-aling:center;'>Empleado modificado exitosamente!</h2>";
            } else {
                echo "<h2 style='text-aling:center;'>Error al actualizar el empleado.</h2>";
            }
        }
    }
    
    echo "<div class='button-group' style='margin-top:50px;'>";
    echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='inicioEmp.php'>Empleados Activos</a></button>";
    echo "</div>";
?>
