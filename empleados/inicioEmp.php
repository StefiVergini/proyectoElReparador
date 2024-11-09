<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("empleados_class.php");

    $busquedaRealizada = false;
    
    $empleadosModel = new Empleados($base);
    $empleadosModel->agregarColumnaSiNoExiste('historial_empleados', 'descripcion_cambio', 'VARCHAR(255) NULL');


    $empleados = [];
    // Manejar la búsqueda
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['buscar'])) {
        $buscar = $_POST['buscar'];
        if (strlen($buscar) < 3 && !is_numeric($buscar)) {
            echo "<h3>Error: Ingrese al menos 3 caracteres para buscar por nombre.</h3>";
        }else {
            // Realizar la búsqueda
            $empleados = $empleadosModel->buscarEmpleados($buscar);
            $busquedaRealizada = true;
        }
    }else{
        // Obtener todos los empleados
        $empleados = $empleadosModel->leerEmpleados();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/tablas.css" />
    <script src="../static/js/funciones_select_nav.js"></script>

</head>
<body class="body">
<h1 class="titulo">Equipo de Trabajo</h1>
    
    <div class="form-container">
        <form action="" method="post">
            <input class="input" type="text" name="buscar" placeholder="Buscar por DNI o nombre" id="buscar" required>
            <button class="btn-iconos" type="submit"><img src="../static/images/lupa.png" alt="Buscar" width='30' height='20' /></button>
        </form>
    </div>


    <div class="table-container">

        <?php if($rol == 2 || $rol == 4 || $rol == 5 || $rol == 6): ?>
            <button class="boton-agregar"> Agregar
                <a class="btn-iconos" href="altaEmp.php"><img src="../static/images/agregar.png" alt="agregar" title="Agregar Empleado" width="30" height="30"></a>
            </button>
        <?php endif; ?>
        <table class="tabla">
            <thead>
                <tr>
                    <th class="tabla-head">Id</th>
                    <th class="tabla-head">DNI</th>
                    <th class="tabla-head">Nombre</th>
                    <th class="tabla-head">Apellido</th>
                    <th class="tabla-head">Teléfono</th>
                    <th class="tabla-head">Email</th>
                    <th class="tabla-head">Sucursal</th>
                    <th class="tabla-head">Fecha Inicio</th>
                    <th class="tabla-head">Categoría</th>
                    <th class="tabla-head">Tipo</th>
                    <?php if($rol == 2 || $rol == 4 || $rol == 5 || $rol == 6): ?>
                        <th class="tabla-head">Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($empleados)): ?>
                    <?php foreach ($empleados as $emp): ?>
                        <?php 
                            $empleado = $emp['empleado'];
                            $historial = $emp['historial'];
                            $categoria = $emp['categoria'];
                            $fecha_inicio = date("d-m-Y", strtotime($historial->getFechaInicio()));
                        ?>
                        <tr>
                            <td class="tabla-data"><?= $empleado->getIdEmp(); ?></td>
                            <td class="tabla-data"><?= $empleado->getDniEmp(); ?></td>
                            <td class="tabla-data"><?= $empleado->getNomEmp(); ?></td>
                            <td class="tabla-data"><?= $empleado->getApeEmp(); ?></td>
                            <td class="tabla-data"><?= $empleado->getTelEmp(); ?></td>
                            <td class="tabla-data"><?= $empleado->getEmailEmp(); ?></td>
                            <td class="tabla-data"><?= $empleado->getIdLocal(); ?></td>
                            <td class="tabla-data"><?= $fecha_inicio; ?></td>
                            <td class="tabla-data"><?= $historial->getIdCategoria(); ?></td>
                            <td class="tabla-data"><?= $categoria->getTipoEmp(); ?></td>
                            <?php if($rol == 2 || $rol == 4 || $rol == 5 || $rol == 6): ?>
                                <td class="tabla-data">
                                    <form action='modificarEmp.php' method='post' style='display:inline;'>
                                        <input type='hidden' name='id' value='<?= $empleado->getIdEmp(); ?>'>
                                        <button type='submit' class="btn-iconos">
                                            <img src='../static/images/editar.png' alt='modificar' title='Editar Empleado' width='20' height='20'>
                                        </button>
                                    </form>
                                    <form action='bajaEmp.php' method='post' style='display:inline;'>
                                        <input type='hidden' name='id' value='<?= $empleado->getIdEmp(); ?>'>
                                        <button type='submit' class="btn-iconos">
                                            <img src='../static/images/borrar.png' alt='eliminar' title='Eliminar Empleado' width='20' height='20'>
                                        </button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td class ="tabla-data" colspan="11">No hay datos</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if($busquedaRealizada): ?>
        <br>
        <button class='btn' style="display:block; margin-left: auto; margin-right:auto;"><a style='text-decoration:none; color:white;' href='inicioEmp.php'>Empleados</a></button><br>
    <?php endif; ?>

<?php
  require "../footer.php";
?>
</body>
</html>