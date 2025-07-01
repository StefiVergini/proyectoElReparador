<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("../proveedores/proveedores_class.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Pedido</title>
    <link rel="stylesheet" href="../static/styles/style.css" /> 
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <link rel="stylesheet" href="../static/styles/tablas.css" />
    <script src="../static/js/funciones_empleados.js"></script>
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>

<div class="formulario-contenedor">
            <h1>Nuevo Pedido</h1><br>
            <h2>Buscar Proveedor: </h2>
            <br>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            
                <div class="form-group">
                    <label class="label" for="buscar_por">Seleccione una Opción de Búsqueda</label>
                    <br>
                    <div class="radio-group">
                        <span class="label">ID</span>
                        <input type="radio" name="buscar_por" id="buscar_por_id" value="n_id" onclick="mostrarCampoBusqueda()"> 
                        <span class="label">CUIT</span>
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

                <!-- Campo de búsqueda por CUIT (oculto por defecto) -->
                <div id="campo_dni" style="display:none;">
                    <div class="form-group">
                        <label class="label" for="dni">Ingrese CUIT: </label>
                        <input class = "input" type="text" name="dni"><br>
                    </div>
                </div>
                <div class="button-group">
                    <input class = "boton submit" type="submit" value="Buscar">
                </div>
            </form>

            <?php
                $proveedores = new Proveedores($base);

                // Comprobar si el formulario ha sido enviado
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && (!empty($_POST['n_id']) || !empty($_POST['dni']))) {
                    // Comprobar si se ha enviado el formulario y si las claves 'n_id' o 'dni' están definidas
                    $id = isset($_POST['n_id']) ? $_POST['n_id'] : null;
                    $cuit = isset($_POST['dni']) ? $_POST['dni'] : null;

                    // Realizar la búsqueda según la opción seleccionada
                    if ($cuit) {
                        $proveedor = $proveedores->obtenerUnCuit($cuit);
                    } elseif ($id) {
                        $proveedor = $proveedores->obtenerUnProv($id);
                    }

                    // Comprobar si se ha encontrado un proveedor
                    if (!empty($proveedor) && is_object($proveedor)) {
                        $estadoProv = $proveedor->getEstadoProv();
                        if($estadoProv == 0){
                            echo "<br><br>";
                            echo "<div class='form-group'>";
                            echo "<label class='label'>El Proveedor que busca se encuentra deshabilitado.</label>";
                            echo "<br><br>";
                            echo "<label class='label'>Debe actualizar su estado o realizar un pedido con otro Proveedor.</label>";
                            echo "</div>";

                            echo "<div class='button-group' style='margin-top:50px;'>";
                            echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='../proveedores/inicioProv.php'>Ver Proveedores</a></button>";
                            echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='pedidosActivos.php'>Pedidos Activos</a></button>";
                            echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='historialPedidos.php'>Historial de Pedidos</a></button>";
                            echo "</div>";
                        }else{

                        
            ?>                               
                            <!-- Formulario de modificación solo si se ha encontrado un proveedor -->
                            <form action="guardarNuevoPedido.php" method="post">
                                
                                <div class="form-group"><p class="label">ID: <?php echo $proveedor->getIdProv(); ?></p></div>
                                <div class="form-group"><p class="label">CUIT: <?php echo $proveedor->getCuit(); ?></p></div>
                                <div class="form-group"><p class="label">Nombre: <?php echo $proveedor->getNomProv(); ?></p></div>
                                <div class="form-group"><p class="label">Teléfono: <?php echo $proveedor->getTelProv(); ?></p></div>
                                <div class="form-group"><p class="label">Dirección: <?php echo $proveedor->getDirProv(); ?></p></div>
                                <div class="form-group"><p class="label">Email: <?php echo $proveedor->getEmailProv(); ?></p></div>
                                <div class="form-group"><p class="label">Saldo: $<?php echo $proveedor->getSaldo(); ?></p></div>
</div>

                                  <?php
                                    $id = $proveedor->getIdProv();
                                    $articulos = $proveedor->leerArtProv($id);


                                    if (empty($articulos)){
                                        echo '<div class="table-container"  style="margin-top: 1rem;">';
                                        echo "<br><br>";
                                        echo "<div class='form-group'>";
                                        echo "<label class='label' style='max-width:50%;'>El Proveedor no tiene Artículos cargados.</label>";
                                        echo "</div>";
                
                                        echo "<div class='button-group' style='margin-top:50px;'>";
                                        echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='../stock/altaStock.php'>Cargar Articulos</a></button>";
                                        echo "<button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='pedidosActivos.php'>Volver</a></button>";
                                        echo "</div>";
                                        echo "</div>";
                                    }else{
                                        ?>
                                        <div class="table-container"  style="margin-top: 1rem;">
                                            <table class="tabla">
                                                <tr>
                                                    <th class="tabla-head">Id Artículo</th>
                                                    <th class="tabla-head">Descripción</th>
                                                    <th class="tabla-head">Cantidad Disponible</th>
                                                    <th class="tabla-head">Cantidad a Pedir</th>
                                                </tr>

                                                <?php foreach ($articulos as $articulo) { ?>
                                                    <tr>
                                                        <td class='tabla-data'><?php echo $articulo['idStock']; ?></td>
                                                        <td class='tabla-data'><?php echo $articulo['art']; ?></td>
                                                        <td class='tabla-data'><?php echo $articulo['cantDispo']; ?></td>
                                                        <td class='tabla-data'>
                                                            <input type='hidden' name='id_proveedor' value='<?php echo $id; ?>'>
                                                            <input type='hidden' name='id_stock[]' value='<?php echo $articulo["idStock"]; ?>'>
                                                            <input type='number' name='cant[]' style='text-align:center; border: solid black 2px; border-radius:5px; height:30px; margin-top:1px; margin-bottom:1px;'>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                        </table>
                                     </div>
                                <div class="table-container"  style="margin-top: 1rem;">
                                    <div class="button-group">               
                                        <input class="boton submit" type="submit" value="Realizar Pedido">
                                        <button class="boton cancelar" type="button" onclick="window.location.href='pedidosActivos.php'">Cancelar</button>
                                    </div> 
                                </div> 
                                <?php
                                    }   
                                ?>
                            </form>

                <?php

                            }
                    } else {
                        // Solo mostrar el mensaje si se hizo una búsqueda y no se encontró ningún empleado
                        echo "<br><br>";
                        echo "<div class='form-group'>";
                        echo "<label class='label'>No se ha encontrado Proveedor con el ID o CUIT indicado</label>";
                        echo "</div>";

                        echo "<div class='button-group' style='margin-top:50px;'>";
                        echo "<br><button class='boton submit' style='margin-right:auto; margin-left:auto;'><a style='text-decoration:none; color:white;' href='inicioProv.php'>Volver a Proveedores</a></button>";
                        echo "</div>";
                    }
                }
                ?>
    
    <?php
        require "../footer.php";
    ?>

</body>
</html>