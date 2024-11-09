<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("empleados_class.php");
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
    <script src="../static/js/funciones_empleados.js"></script>
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <main>
        <div class="formulario-contenedor">
            <h1>Cambiar Categoría de un Empleado</h1>
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
                        $historial = $historialModel->leerCatFecha($id);
            ?>                               
                        <!-- Formulario de modificación solo si se ha encontrado un empleado -->
                        <form action="guardarCambioCat.php" method="post"> 
                            <div class="form-group"><p class="label">ID: <?php echo $empleado->getIdEmp(); ?></p></div>
                            <div class="form-group"><p class="label">DNI: <?php echo $empleado->getDniEmp(); ?></p></div>
                            <div class="form-group"><p class="label">Nombre y Apellido: <?php echo $empleado->getNomEmp(); ?> <?php echo $empleado->getApeEmp(); ?></p></div>
                            <div class="form-group"><p class="label">Teléfono: <?php echo $empleado->getTelEmp(); ?></p></div>
                            <div class="form-group"><p class="label">Dirección: <?php echo $empleado->getDirEmp(); ?></p></div>
                            <div class="form-group"><p class="label">Email: <?php echo $empleado->getEmailEmp(); ?></p></div>
                            <div class="form-group"><p class="label">Categoría 
                                <?php 
                                    $categorias = $categoriasEmp->leerCategorias();
                                    $catEmp = $historial[0]['historial']->getIdCategoria();
                                    foreach ($categorias as $categoria) {
                                        $cat = $categoria->getIdCat();
                                        if($cat == $catEmp){
                                            echo " N°: '".$catEmp."' - Tipo: '".$categoria->getTipoEmp()."'";

                                        }
                                    }

                                ?>
                                </p></div>
                            <div class="form-group"><p class="label">Fecha Inicio del Puesto: <?php echo date('d-m-Y', strtotime($historial[0]['historial']->getFechaInicio())); ?></p></div>                          
                            <input type="hidden" name="n_id" value="<?php echo $empleado->getIdEmp(); ?>">
                            <div class="form-group">
                                <label class="label" for="fecha_fin">Fecha Finalización del Puesto</label>
                                <input class="input" type="date" name="fecha_fin" id="fecha_fin" required>
                            </div>  
                            <div class="form-group">
                                <label class="label" for="desc">Descripción del Cambio de Categoría:</label>
                                <textarea class="input" name="desc" id="desc" cols="20" rows="6"></textarea>
                            </div>  
                            <div class="form-group">    
                                <label class="label" for="fecha_inicio">Fecha de Inicio del Nuevo Puesto:</label>
                                <input class="input" type="date" name="fecha_inicio" id="fecha_inicio" required>
                            </div>  
                            <div class="form-group">
                                <label class="label" for="categoria">Nueva Categoría</label>
                                <select name="categoria" id="categoria">
                                <?php 
                                    $categorias = $categoriasEmp->leerCategorias();
                                    foreach ($categorias as $categoria) {
                                        $cat = $categoria->getIdCat();
                                        if($cat != $catEmp){
                                            echo '<option value="' . $cat . '">' . $categoria->getTipoEmp() . '</option>';
                                        }
                                    }
                                ?>
                                </select>
                            </div>  
                            <div class="button-group">                      
                                <input class="boton submit" type="submit" value="Modificar">
                                <button class="boton cancelar" type="button" onclick="window.location.href='inicioEmp.php'">Cancelar</button>
                            </div>      
                        </form>
                <?php
                    } else {
                        // Solo mostrar el mensaje si se hizo una búsqueda y no se encontró ningún empleado
                        echo "<div class='form-group'>";
                        echo "<label class='label'>No se ha encontrado empleado con el ID o DNI indicado</label>";
                        echo "</div>";
                    }
                }
                ?>
        </div>
    </main>
    <?php
    require "../footer.php";
    ?>
</body>
</html>