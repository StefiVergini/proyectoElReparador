<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("proveedores_class.php");

    $busquedaRealizada = false;
    
    $proveedoresModel = new Proveedores($base);


    $proveedores = [];
    // Manejar la búsqueda
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['buscar'])) {
        $buscar = $_POST['buscar'];
        if (strlen($buscar) < 3 && !is_numeric($buscar)) {
            echo "<h3>Error: Ingrese al menos 3 caracteres para buscar por nombre.</h3>";
        }else {
            // Realizar la búsqueda
            $proveedores = $proveedoresModel->buscarProveedores($buscar);
            $busquedaRealizada = true;
        }
    }else{
        // Obtener todos los proveedores
        $proveedores = $proveedoresModel->leerProveedores();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/tablas.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>

    <h1  class="titulo">Proveedores</h1>
    <div class="div-con-botones">
        <div class="form-container">
            <div class="desplegable" style="margin-right: 5rem;">
                <button class="btn">Exportar</button>
                <div class="link">
                    <a target="_blank" href="pdf_prov.php">pdf</a>
                    <a class="excel" href="excel_prov.php">excel</a>
                </div>
            </div>
            <div style="margin-right: 5rem;">
                <form action="" method="post">
                    <input class="input" type="text" name="buscar" placeholder="Buscar por CUIT o nombre" id="buscar" required>
                    <button class="btn-iconos" type="submit"><img src="../static/images/lupa.png" alt="Buscar" width='30' height='20' /></button>
                </form>
            </div>
            <button class="btn"> Agregar
                <a class="btn-iconos" href="altaProv.php"><img src="../static/images/agregar.png" alt="agregar" title="Agregar Proveedor" width="30" height="30"></a>
            </button>

        </div>
    </div>
    <div class="table-container"  style="margin-top: 1rem;">

        <table class="tabla">
            <tr>
                <th class="tabla-head">Id</th>
                <th class="tabla-head">CUIT</th>
                <th class="tabla-head">Nombre</th>
                <th class="tabla-head">Teléfono</th>
                <th class="tabla-head">Dirección</th>
                <th class="tabla-head">Email</th>
                <th class="tabla-head">Saldo</th>
                <th class="tabla-head">Estado</th>
                <?php
                if($rol == 2 || $rol == 4 || $rol == 5 || $rol == 6){
                    ?>
                    <th class="tabla-head">Acciones</th>
                    <?php
                }
                ?>
            </tr>
            <?php
                if (!empty($proveedores)){
                    foreach ($proveedores as $proveedor) {
                        echo "<tr>";
                        echo "<td class='tabla-data'>" . $proveedor->getIdProv() . "</td>";
                        echo "<td class='tabla-data'>" . $proveedor->getCuit() . "</td>";
                        echo "<td class='tabla-data'>" . $proveedor->getNomProv() . "</td>";
                        echo "<td class='tabla-data'>" . $proveedor->getTelProv() . "</td>";
                        echo "<td class='tabla-data'>" . $proveedor->getDirProv() . "</td>";
                        echo "<td class='tabla-data'>" . $proveedor->getEmailProv() . "</td>";
                        echo "<td class='tabla-data'>$ " . $proveedor->getSaldo() . "</td>";
                        echo "<td class='tabla-data'>" . ($proveedor->getEstadoProv() == 1 ? 'Activo' : 'Inactivo') . "</td>";
                        if($rol == 2 || $rol == 4 || $rol == 5 || $rol == 6){
                            if ($proveedor->getEstadoProv() == 1) {
                                // Si está activo (estado == 1), muestra los botones de editar y eliminar
                                echo "<td class='tabla-data'>
                                        <form action='modificarProv.php' method='post' style='display:inline;'>
                                            <input type='hidden' name='id' value='" . $proveedor->getIdProv() . "'>
                                            <button type='submit' class='btn-iconos'>
                                                <img src='../static/images/editar.png' alt='modificar' title='Editar Proveedor' width='20' height='20'>
                                            </button>
                                        </form>
                                        |   
                                        <form action='eliminarProv.php' method='post' style='display:inline;' onsubmit='return confirmarEliminacion(\"" . $proveedor->getNomProv() . "\", \"" . $proveedor->getCuit() . "\")'>
                                        <input type='hidden' name='id' value='" . $proveedor->getIdProv() . "'>
                                        <button type='submit' class='btn-iconos'>
                                            <img src='../static/images/borrar.png' alt='eliminar' title='Eliminar Proveedor' width='20' height='20'>
                                        </button>
                                        </form>
                                    </td>";
                            } else {
                                // Si está inactivo (estado == 0), deshabilita el botón de editar y cambia el botón de eliminar por actualizar
                                echo "<td class='tabla-data'>
                                        <form action='modificarProv.php' method='post' style='display:inline;'>
                                            <input type='hidden' name='id' value='" . $proveedor->getIdProv() . "'>
                                            <button type='submit' class='btn-iconos' disabled>
                                                <img src='../static/images/editar.png' alt='modificar' title='Editar Proveedor' width='20' height='20'>
                                            </button>
                                        </form>
                                        |
                                        <form action='actualizarProv.php' method='post' style='display:inline;'  onsubmit='return confirmarActualizacion(\"" . $proveedor->getNomProv() . "\", \"" . $proveedor->getCuit() . "\")'>
                                            <input type='hidden' name='id' value='" . $proveedor->getIdProv() . "'>
                                            <button type='submit' class='btn-iconos'>
                                                <img src='../static/images/actualizar.png' alt='actualizar' title='Actualizar Proveedor' width='20' height='20'>
                                            </button>
                                        </form>
                                    </td>";
                            }
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td class ='tabla-data' colspan='11'>No hay datos</td></tr>";
                }
            ?>
        </table>
    </div>
    <?php
        if($busquedaRealizada){
            echo "<br><button class='btn' style='display:block; margin-left: auto; margin-right:auto;'><a style='text-decoration:none; color:white;' href='inicioProv.php'>Proveedores</a></button>";
        }
        require '../footer.php';
    ?>

</body>
</html>
<script>
    function confirmarEliminacion(nombre, cuit) {
        return confirm(`¿Estás seguro que deseas dar de baja al proveedor "${nombre}" con CUIT ${cuit}?`);
    }
    function confirmarActualizacion(nombre, cuit) {
        return confirm(`¿Estás seguro que deseas volver a dar de Alta al proveedor "${nombre}" con CUIT ${cuit}?`);
    }
</script>

