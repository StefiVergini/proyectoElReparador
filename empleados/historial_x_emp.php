<?php
    include("../header.php");
    include("../conexionPDO.php");
    include_once("empleados_class.php");
    include_once("categorias_emp_class.php");
    include_once("historial_emp_class.php");
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Categoría</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <link rel="stylesheet" href="../static/styles/tablas.css" />
    <script src="../static/js/funciones_empleados.js"></script>
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <main>
        <div class="formulario-contenedor">
            <h1>Registro Histórico de un Empleado</h1>
            <h2>Buscar Empleado: </h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="form-group">
                    <label class="label" for="buscar_por">Seleccione una Opción de Búsqueda</label>
                    <br>
                    <div class="radio-group">
                        <span class="label">ID</span>
                        <input type="radio" name="buscar_por" id="buscar_por_id" value="n_id" onclick="mostrarCampoBusqueda()"> 
                        <span class="label">DNI</span>
                        <input type="radio" name="buscar_por" id="buscar_por_dni" value="dni" onclick="mostrarCampoBusqueda()">
                    </div>
                </div>

                <!-- Campo de búsqueda por ID (oculto por defecto) -->
                <div id="campo_id" style="display:none;">
                    <div class="form-group">
                        <label class="label" for="n_id">Ingrese ID: </label>
                        <input class = "input" type="text" name="n_id"><br>
                    </div>
                </div>

                <!-- Campo de búsqueda por DNI (oculto por defecto) -->
                <div id="campo_dni" style="display:none;">
                    <div class="form-group">
                        <label class="label" for="dni">Ingrese DNI: </label>
                        <input class = "input" type="text" name="dni"><br>
                    </div>
                </div>

                <div class="button-group">
                    <input class = "boton submit" type="submit" value="Buscar">
                </div>
                <br>
            </form>
            <?php
                $empleados = new Empleados($base);
                $historialModel = new HistorialEmp($base);
                $categoriasEmp = new CategoriasEmp($base);

                // Comprobar si el formulario ha sido enviado
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // Comprobar si se ha enviado el formulario y si las claves 'n_id' o 'dni' están definidas
                    $id = isset($_POST['n_id']) ? $_POST['n_id'] : null;
                    $dni = isset($_POST['dni']) ? $_POST['dni'] : null;

                    // Realizar la búsqueda según la opción seleccionada
                    if ($dni) {
                        $empleado = $empleados->obtenerUnEmpDni($dni);
                    } elseif ($id) {
                        $empleado = $empleados->obtenerUnEmp($id);
                    }

                    // Comprobar si se ha encontrado un empleado
                    if (!empty($empleado) && is_object($empleado)) {
                        $historial = $historialModel->registroHistorico($id);
            ?>
                    <form action="">
                        <div class="form-group"><p>ID: <?php echo $empleado->getIdEmp(); ?></p></div>
                        <div class="form-group"><p>DNI: <?php echo $empleado->getDniEmp(); ?></p></div>
                        <div class="form-group"><p>Nombre y Apellido: <?php echo $empleado->getNomEmp(); ?> <?php echo $empleado->getApeEmp(); ?></p></div>
                        <div class="form-group"><p>Teléfono: <?php echo $empleado->getTelEmp(); ?></p></div>
                        <div class="form-group"><p>Dirección: <?php echo $empleado->getDirEmp(); ?></p></div>
                        <div class="form-group"><p>Email: <?php echo $empleado->getEmailEmp(); ?></p></div>
                    </form>
        </div>         

                        <div class="table-container">
                            <table class="tabla">
                                <tr>
                                    <th class="tabla-head">Id Categoría</th>
                                    <th class="tabla-head">Tipo</th>
                                    <th class="tabla-head">Fecha Inicio del Puesto</th>
                                    <th class="tabla-head">Fecha Fin del Puesto</th>
                                    <th class="tabla-head">Descripción</th>
                                </tr>
                                <?php
                                    if (!empty($historial)){
                                        foreach ($historial as $emp) {
                                            $hist = $emp['historial'];
                                            $categoria = $emp['categoria'];
                                            $fecha_inicio = $hist->getFechaInicio();
                                            $fecha_inicio_mysql = date("d-m-Y", strtotime($fecha_inicio));
                                            $fecha_fin = $hist->getFechaFin();
                                            $fecha_fin_mysql = date("d-m-Y", strtotime($fecha_fin));
                                            $descripcion = $hist->getDescripcion();
                                            echo "<tr>";
                                            echo "<td class='tabla-data'>" . $hist->getIdCategoria() . "</td>";
                                            echo "<td class='tabla-data'>" . $categoria->getTipoEmp() . "</td>";
                                            echo "<td class='tabla-data'>" . $fecha_inicio_mysql . "</td>";
                                        
                                            if($fecha_fin !== null){
                                                echo "<td class='tabla-data'>" . $fecha_fin_mysql . "</td>";
                                            }else{
                                                echo "<td class='tabla-data'>     -      </td>";
                                            }
                                            
                                            if($descripcion !== null){
                                                echo "<td class='tabla-data'>" . $descripcion. "</td>";
                                            }else{
                                                echo "<td class='tabla-data'>     -      </td>";
                                            }                                                  
                                        }
                                    } else {
                                        echo "<div class='form-group'>";
                                        echo "<label class='label'>No hay datos</label>";
                                        echo "</div>";
                                    }
                                    ?>
                            </table>
                        </div>
                        <?php
                    }else{
                        echo "<div class='form-group'>";
                        echo "<label class='label'>No se ha encontrado empleado con el ID o DNI indicado</label>";
                        echo "</div>";
                    }
                }
            
            require "../footer.php";

        ?>
        
    </main>

</body>
</html>