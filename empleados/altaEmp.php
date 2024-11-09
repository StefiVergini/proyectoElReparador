<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("categorias_emp_class.php");
    include("empleados_class.php");
    $categoriasEmp = new CategoriasEmp($base);
    $locales_base = new Empleados($base);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Empleado</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <script src="../static/js/funciones_select_nav.js"></script>   
   
</head>
<body>
    <main>
        <div class="formulario-contenedor">
            <h1>Nuevo Empleado</h1>
        
            <form action="guardarAltaEmp.php" method="post">
                
                <div class="form-group">
                    <label class="label" for="dni">DNI</label>
                    <input class="input" type="number" name="dni" min="10000000" max="99999999" required>
                </div>
                <div class="form-group">
                    <label class="label" for="nombre">Nombre</label>
                    <input class="input" type="text" name="nombre" id="nombre" required>
                </div>
                <div class="form-group">
                    <label class="label" for="apellido">Apellido</label>
                    <input class="input" type="text" name="apellido" id="apellido" required>
                </div>
                <div class="form-group">   
                    <label class="label" for="telefono">Teléfono</label>
                    <input class="input" type="text" name="telefono" id="telefono" required>
                </div>
                <div class="form-group">
                    <label class="label" for="email">Email</label>
                    <input class="input" type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label class="label" for="direccion">Dirección</label>
                    <input class="input" type="text" name="direccion" id="direccion" required>
                </div>
                <div class="form-group">
                    <label class="label" for="fecha_ini">Fecha Inicio del Puesto</label>
                    <input class="input" type="date" name="fecha_ini" id="fecha_ini" required>
                </div>

                <div class="form-group">
                    <label class="label" for="categoria">Categoría</label>
                    <select name="categoria" id="categoria">
                        <?php 
                                    
                            $categorias = $categoriasEmp->leerCategorias();

                            foreach ($categorias as $categoria) {
                                echo '<option value="' . $categoria->getIdCat() . '">' . $categoria->getTipoEmp() . '</option>';
                            }
                        
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="label" for="id_local">Sucursal</label><br>
                        <div class="radio-group">
                            <?php
                                $locales = $locales_base->leerLocales();
                                foreach ($locales as $local) {
                                    echo '<label><input type="radio" name="id_local" value="' . $local['idlocal'] . '" required> ' . $local['idlocal'] . ' - ' . $local['dir_local'] . '</label>';
                                }
                            ?>
                        </div>
                </div>

                
                <div class="button-group">
                    <input class="boton submit" type="submit" value="Agregar">
                    <button class= "boton cancelar"> <a href="inicioEmp.php" style="text-decoration:none;">Cancelar</a></button>
                </div>
            </form>
        </div>
    </main>
    <?php
    require "../footer.php";
    ?>
</body>
</html>