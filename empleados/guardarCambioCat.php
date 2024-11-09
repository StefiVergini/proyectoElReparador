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
    $id = $_POST['n_id'];
    $fecha_fin = $_POST['fecha_fin'];
    $categoria_id = $_POST['categoria'];
    $fecha_inicio = $_POST['fecha_inicio'];

    $estado_emp = 1;

    // Formatear la fecha para MySQL
    $fecha_fin_mysql = date("Y-m-d", strtotime($fecha_fin));
    $fecha_inicio_mysql = date("Y-m-d", strtotime($fecha_inicio));
    
    $empleado = new HistorialEmp($base);

    $empleado->setIdEmp($id);
    $empleado->setFechaFin($fecha_fin_mysql);
    $empleado->setEstadoEmp($estado_emp);
    $empleado->setDescripcion(isset($_POST['desc']) ? $_POST['desc'] : null);
    $empleado->setIdCategoria($_POST['categoria']);
    $empleado->setFechaInicio($fecha_inicio_mysql);

    if ($empleado->bajaEmpleado($id) && $empleado->altaHistorial()) {
        echo "<h2 style='text-align:center;'>Se ha cambiado la categoría del Empleado con éxito!</h2>";
    }else{
        echo "<h2 style='text-align:center'>UPS! Ha ocurrido un error al cambiar la categoría del Empleado.</h2>";
    }

    echo "<div class='button-group' style='margin-top:50px;'>";
    echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='inicioEmp.php'>Empleados Activos</a></button>";
    echo "</div>";
}
?>