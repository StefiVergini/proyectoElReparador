<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("empleados_class.php");
    include_once("categorias_emp_class.php");
    include_once("historial_emp_class.php");

    $categoriasEmp = new CategoriasEmp($base);
    $historialModel = new HistorialEmp($base);
    $id = $_POST['id'];
    $empleadosModel = new Empleados($base);

    $empleado = $empleadosModel->obtenerUnEmp($id);
    if (!empty($empleado)) {
        $historial = $historialModel->leerCatFecha($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Empleado</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <main>
        <div class="formulario-contenedor ">


            <h1>Modificar Datos del Empleado</h1>
        
            <form action="guardarModiEmp.php" method="post">
                <input type="hidden" name="id" value="<?php echo $empleado->getIdEmp(); ?>">
                
                <div class="form-group">   
                    <label class="label" for="dni">DNI</label>
                    <input class="input" type="number" name="dni" min="10000000" max="99999999" value="<?php echo $empleado->getDniEmp(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="nombre">Nombre</label>
                    <input class="input" type="text" name="nombre" value="<?php echo $empleado->getNomEmp(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="apellido">Apellido</label>
                    <input class="input" type="text" name="apellido" value="<?php echo $empleado->getApeEmp(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="telefono">Teléfono</label>
                    <input class="input" type="text" name="telefono" value="<?php echo $empleado->getTelEmp(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="direccion">Dirección</label>
                    <input class="input" type="text" name="direccion" value="<?php echo $empleado->getDirEmp(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="email">Email</label>
                    <input class="input" type="email" name="email" value="<?php echo $empleado->getEmailEmp(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="fecha_ini">Fecha Inicio del Puesto</label>
                    <input class="input" type="date" name="fecha_ini" id="fecha_ini" value="<?php echo date('Y-m-d', strtotime($historial[0]['historial']->getFechaInicio())); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="id_local">Sucursal</label>
                    <div class="radio-group">
                        <?php
                            $locales = $empleadosModel->leerLocales();
                            foreach ($locales as $local) {
                                $checked = ($local['idlocal'] == $empleado->getIdLocal()) ? 'checked' : '';
                                echo '<input type="radio" name="id_local" value="' . $local['idlocal'] . '" ' . $checked . '> '. $local['idlocal']  ." - ". $local['dir_local'] . '<br>';
                            }
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label" for="categoria">Categoría</label>
                    <select name="categoria" id="categoria">
                    <?php 
                        $categorias = $categoriasEmp->leerCategorias();
                        foreach ($categorias as $categoria) {
                            $selected = ($categoria->getIdCat() == $historial[0]['historial']->getIdCategoria()) ? 'selected' : '';
                            echo '<option value="' . $categoria->getIdCat() . '" ' . $selected . '>' . $categoria->getTipoEmp() . '</option>';
                        }
                    ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="label" for="desc">Descripción del Cambio</label>
                    <textarea class="input" name="desc" id="desc" rows="4" cols="20">
                        <?php 
                        // Si la descripción no es nula, la muestra; de lo contrario, deja el campo vacío.
                        echo !is_null($historial[0]['historial']->getDescripcion()) ? htmlspecialchars($historial[0]['historial']->getDescripcion()) : '';
                        ?>
                    </textarea>
                </div>
                
                <div class="button-group">
                    <input class="boton submit" type="submit" value="Modificar">
                    <button class="boton cancelar"> <a href="inicioEmp.php">Cancelar</a></button>
                </div>
            </form>

            <br>
            <br>
            <br>

        <?php
        } else {
            echo "<h1>No hay datos para mostrar</h1>";
            echo "<br><button class='btn'><a style='text-decoration:none; color:white;' href='inicioEmp.php'>Volver</a></button>";
        }
        ?>
        </div>
    </main>
</body>
</html>