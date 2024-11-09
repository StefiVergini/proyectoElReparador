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
    $fecha_inicio = $_POST['fecha_inicio'];

    $estado_emp = 1;

    // Formatear la fecha para MySQL
    $fecha_inicio_mysql = date("Y-m-d", strtotime($fecha_inicio));
    
    $empleado = new HistorialEmp($base);

    $empleado->setIdEmp($id);
    $empleado->setFechaInicio($fecha_inicio_mysql);
    $empleado->setEstadoEmp($estado_emp);
    $empleado->setDescripcion($_POST['desc']);
    $empleado->setIdCategoria($_POST['id_cat']);

    if ($empleado->altaHistorial($id)) {
        echo "<h2 style='text-align:center;'>Se ha Reincorporado al Empleado.</h2>";
    }else{
        echo "<h2 style='text-align:center;'>UPS! Ha ocurrido un error al intentar reincorporar al empleado.</h2>";
    }
    
    echo "<div class='button-group' style='margin-top:50px;'>";
    echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='inicioEmp.php'>Volver Empleados Activos</a></button>";
    echo "</div>";

}
?>