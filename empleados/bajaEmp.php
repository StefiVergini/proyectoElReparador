<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("empleados_class.php");

    $id = $_POST['id'];
    $empleadosModel = new Empleados($base);

    $empleado = $empleadosModel->obtenerUnEmp($id);
    
    if (!empty($empleado)) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baja Empleado</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <main>
        <div class="formulario-contenedor">
            <h1>Baja Empleado</h1>

                <form action="guardarBajaEmp.php" method="post" onsubmit="return confirmarEliminacion('<?php echo $empleado->getNomEmp(); ?>', '<?php echo $empleado->getApeEmp(); ?>', '<?php echo $empleado->getDniEmp(); ?>')">
                    <input type="hidden" name="id" value="<?php echo $empleado->getIdEmp(); ?>">
                    <div class="form-group"><p class="label">DNI: <?php echo $empleado->getDniEmp(); ?></p></div>
                    <div class="form-group"><p class="label">Email: <?php echo $empleado->getEmailEmp(); ?></p></div>
                    <div class="form-group"><p class="label">Nombre: <?php echo $empleado->getNomEmp(); ?></p></div>
                    <div class="form-group"><p class="label">Apellido: <?php echo $empleado->getApeEmp(); ?></p></div>
                    <div class="form-group"><p class="label">Teléfono: <?php echo $empleado->getTelEmp(); ?></p></div>
                    <div class="form-group"><p class="label">Dirección: <?php echo $empleado->getDirEmp(); ?></p></div>

                    <div class="form-group">
                        <label class="label" for="desc">Descripción de la Baja</label>
                        <textarea class="input" name="desc" id="desc" cols="20" rows="6" required></textarea>
                    </div>
                    <div class="form-group">
                        <label class="label" for="fecha_fin">Fecha Finalización del Puesto</label>
                        <input class="input" type="date" name="fecha_fin" id="fecha_fin" required>
                    </div>
                    <div class="button-group">
                        <input class="boton submit" type="submit" value="Eliminar">
                        <button class="boton cancelar" type="button" onclick="window.location.href='inicioEmp.php'">Cancelar</button>
                    </div>
                </form>
        <?php
        } else {
            echo "<h1>No hay datos para mostrar</h1>";
            echo "<br><button><a href='inicioEmp.php'>Volver</a></button>";
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
function confirmarEliminacion(nombre, apellido, dni) {
    var confirmacion = confirm(`¿Estás seguro que deseas dar de baja al Empleado ${nombre} ${apellido} con DNI: ${dni}?`);
    if (!confirmacion) {
        window.location.href = 'empInactivos.php';
        return false;
    }
    return true;
}
</script>