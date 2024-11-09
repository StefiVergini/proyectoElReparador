<?php
include("../header.php");
include("../conexionPDO.php");
include("historial_emp_class.php");
?>
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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $fecha_fin = $_POST['fecha_fin'];

    $estado_emp = 0;

    // Formatear la fecha para MySQL
    $fecha_fin_mysql = date("Y-m-d", strtotime($fecha_fin));
    
    $empleado = new HistorialEmp($base);

    $empleado->setIdEmp($id);
    $empleado->setFechaFin($fecha_fin_mysql);
    $empleado->setEstadoEmp($estado_emp);
    $empleado->setDescripcion($_POST['desc']);

    if ($empleado->bajaEmpleado($id)) {
        echo "<h1 style='text-align:center;';>Empleado dado de Baja.</h1>";
    }else{
        echo "<h1 style='text-align:center;';>UPS! Ha ocurrido un error al intentar dar de baja al empleado.</h1>";
    }
    echo "<div class='button-group' style='margin-top:50px;'>";
    echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='inicioEmp.php'>Volver Empleados Activos</a></button>";
    echo "<button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='empInactivos.php'>Ver Empleados Antiguos</a></button>";
    echo "</div>";
}
?>