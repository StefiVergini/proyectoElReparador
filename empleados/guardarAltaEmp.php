<?php
include("../header.php");
include("../conexionPDO.php");
include("empleados_class.php");
//include("historial_emp_class.php");
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
    $categoria_id = $_POST['categoria'];
    $fecha_inicio = $_POST['fecha_ini'];
    $dni = $_POST['dni'];


    //estado empleado automaticamente activo cuando se crea
    $estado_emp = 1;

    // Formatear la fecha para MySQL
    $fecha_inicio_mysql = date("Y-m-d", strtotime($fecha_inicio));
    
    if (!is_numeric($dni) || strlen($dni) !== 8) {
        echo "<h2>Error: El DNI debe ser numérico y contener exactamente 8 dígitos.</h2>";
    }else{
        $empleado = new Empleados($base);

        $empleado->setDniEmp($dni);
        $empleado->setNomEmp($_POST['nombre']);
        $empleado->setApeEmp($_POST['apellido']);
        $empleado->setTelEmp($_POST['telefono']);
        $empleado->setEmailEmp($_POST['email']);
        $empleado->setDirEmp($_POST['direccion']);
        $empleado->setIdLocal($_POST['id_local']);

        if ($empleado->altaEmp($categoria_id, $fecha_inicio_mysql, $estado_emp)) {
            echo "<h2 style='text-align:center;'>Empleado creado con éxito.</h2>";
        }
    }

   
    echo "<div class='button-group' style='margin-top:50px;'>";
    echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='inicioEmp.php'>Empleados Activos</a></button>";
    echo "</div>";
}
?>