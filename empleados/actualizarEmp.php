<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("empleados_class.php");
    include_once("historial_emp_class.php");

    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo "Error: No se proporcionó un ID válido.";
        exit;
    }

    $id = $_POST['id'];
    $empleadosModel = new Empleados($base);
    $historialModel = new HistorialEmp($base);

    $empleado = $empleadosModel->obtenerUnEmp($id);
    $historialData = $historialModel->leerCatFecha($id);
    if (!empty($empleado) && !empty($historialData)) {
        $historial = $historialData[0]['historial'];
        $categoria = $historialData[0]['categoria'];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reincorporar Empleado</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <main>
        <div class="formulario-contenedor">
            <h1>Reincorporar Empleado</h1>          
            
            <form action="guardarActualizacionEmp.php" method="post" onsubmit="return confirmarReincorporacion('<?php echo $empleado->getNomEmp(); ?>', '<?php echo $empleado->getApeEmp(); ?>', '<?php echo $empleado->getDniEmp(); ?>')">
                    <div class="form-group"><p class="label">DNI: <?php echo $empleado->getDniEmp(); ?></p></div>
                    <div class="form-group"><p class="label">Email: <?php echo $empleado->getEmailEmp(); ?></p></div>  
                    <div class="form-group"><p class="label">Nombre: <?php echo $empleado->getNomEmp(); ?></p></div>
                    <div class="form-group"><p class="label">Apellido: <?php echo $empleado->getApeEmp(); ?></p></div>
                    <div class="form-group"><p class="label">Teléfono: <?php echo $empleado->getTelEmp(); ?></p></div>
                    <div class="form-group"><p class="label">Dirección: <?php echo $empleado->getDirEmp(); ?></p></div>
                    
                    <input type="hidden" name="id" value="<?php echo $empleado->getIdEmp(); ?>">
                    <input type="hidden" name="id_cat" value="<?php echo $historial->getIdCategoria(); ?>">
                    <div class="form-group">
                        <label class="label" for="fecha_inicio">Fecha de Inicio del Puesto</label>
                        <input class="input" type="date" name="fecha_inicio" id="fecha_inicio" required>
                    </div>
                    <div class="form-group">
                        <label class="label" for="desc">Descripción de la Reincorporación</label>
                        <textarea class="input"name="desc" id="desc" cols="20" rows="6"></textarea>
                    </div>

                    <div class="button-group">
                        <input class="boton submit" type="submit" value="Reincorporar Empleado">
                        <button class= "boton cancelar"  type="button" onclick="window.location.href='empInactivos.php'">Cancelar</button>
                    </div>
            </form>
        <?php
        } else {
            echo "<h1>No hay datos para mostrar</h1>";
            echo "<br><button class='boton submit'><a href='inicioEmp.php'>Volver</a></button>";
        }
        ?>
        </div>
    </main>
    <?php
    require "../footer.php";
    ?>
</body>
</html>

<script>
function confirmarReincorporacion(nombre, apellido, dni) {
    var confirmacion = confirm(`¿Estás seguro que deseas volver a dar de Alta al Empleado ${nombre} ${apellido} con DNI: ${dni}?`);
    if (!confirmacion) {
        window.location.href = 'inicioEmp.php';
        return false;
    }
    return true;
}
</script>