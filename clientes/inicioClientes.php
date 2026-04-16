<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("clientes_class.php");

    $busquedaRealizada = false;
    
    $clientesModel = new Clientes($base);


    $clientes = [];
    // Manejar la búsqueda
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['buscar'])) {
        $buscar = $_POST['buscar'];
        if (strlen($buscar) < 3 && !is_numeric($buscar)) {
            echo "<h3>Error: Ingrese al menos 3 caracteres para buscar por nombre.</h3>";
        }else {
            // Realizar la búsqueda
            $clientes = $clientesModel->buscarClientes($buscar);
            $busquedaRealizada = true;
        }
    }else{
        // Obtener todos los clientes
        $clientes = $clientesModel->leerClientes();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/tablas.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>

    <h1  class="titulo">Clientes</h1>
    <div class="div-con-botones">
        <form action='altaCliente.php' method='post' style='display:inline;'>
            <button type='submit' class='btn'> Agregar +
            </button>
        </form>
        <div class="desplegable">
            <button class="btn">Exportar &#9207;</button>
            <div class="link">
                <a target="_blank" href="pdf.php">pdf</a>
                <a class="excel" href="excel.php">excel</a>
            </div>
        </div>
    </div>
    <div class="div-con-botones">
        <div class="form-group">
            <form action="" method="post">
                <input class="input" type="text" name="buscar" placeholder="Buscar por DNI o Nombre" id="buscar" required>
                    <button class="btn-iconos" type="submit"><img src="../static/images/lupa.png" alt="Buscar" width='30' height='20' /></button>
            </form>
        </div>
    </div>
    <div class="table-container">

        <table class="tabla">
            <tr>
                <th class="tabla-head">Id</th>
                <th class="tabla-head">DNI</th>
                <th class="tabla-head">Nombre</th>
                <th class="tabla-head">Apellido</th>
                <th class="tabla-head">Teléfono</th>
                <th class="tabla-head">Dirección</th>
                <th class="tabla-head">Email</th>
                <th class="tabla-head">Estado</th>
                <th class="tabla-head">Ver Electros</th>
                <?php
                if($rol == 2 || $rol == 4 || $rol == 5 || $rol == 6){
                    ?>
                    <th class="tabla-head">Acciones</th>
                    <?php
                }
                ?>
            </tr>
            <?php
                if (!empty($clientes)){
                    foreach ($clientes as $cliente) {
                        echo "<tr>";
                        echo "<td class='tabla-data'>" . $cliente->getIdCli() . "</td>";
                        echo "<td class='tabla-data'>" . $cliente->getDniCli() . "</td>";
                        echo "<td class='tabla-data'>" . $cliente->getNomCli() . "</td>";
                        echo "<td class='tabla-data'>" . $cliente->getApeCli() . "</td>";
                        echo "<td class='tabla-data'>" . $cliente->getTelCli() . "</td>";
                        echo "<td class='tabla-data'>" . $cliente->getDirCli() . "</td>";
                        echo "<td class='tabla-data'>" . $cliente->getEmailCli() . "</td>";
                        echo "<td class='tabla-data'>" . ($cliente->getEstadoCli() == 1 ? 'Activo' : 'Deshabilitado') . "</td>";
                        echo "<td class='tabla-data'>
                                <form action='verElectrosDeUnCli.php' method='post' style='display:inline;'>
                                    <input type='hidden' name='id' value='" . $cliente->getIdCli() . "'>          
                                    <input type='hidden' name='dniCli' value='" .  $cliente->getDniCli() . "'>
                                    <input type='hidden' name='emailCli' value='" . $cliente->getEmailCli() . "'>
                                    <input type='hidden' name='telCli' value='" . $cliente->getTelCli() . "'>
                                    <button type='submit' class='btn-iconos'>
                                        <img src='../static/images/eye1.png' alt='ver' title='Ver Electros' width='20' height='20'>
                                    </button>
                                </form>
                            </td>";
                        if($rol == 2 || $rol == 4 || $rol == 5 || $rol == 6){
                            if ($cliente->getEstadoCli() == 1) {
                                // Si está activo (estado == 1), muestra los botones de editar y eliminar
                                echo "<td class='tabla-data'>
                                        <form action='modificarCli.php' method='post' style='display:inline;'>
                                            <input type='hidden' name='id' value='" . $cliente->getIdCli() . "'>
                                            <button type='submit' class='btn-iconos'>
                                                <img src='../static/images/editar.png' alt='modificar' title='Editar Cliente' width='20' height='20'>
                                            </button>
                                        </form>
                                        |   
                                        <form action='eliminarCli.php' method='post' style='display:inline;' onsubmit='return confirmarEliminacion(\"" . $cliente->getNomCli() . "\", \"" . $cliente->getApeCli(). "\", \"" . $cliente->getDniCli() . "\")'>
                                        <input type='hidden' name='id' value='" . $cliente->getIdCli() . "'>
                                        <button type='submit' class='btn-iconos'>
                                            <img src='../static/images/borrar.png' alt='eliminar' title='Eliminar Cliente' width='20' height='20'>
                                        </button>
                                        </form>
                                    </td>";
                            } else {
                                // Si está inactivo (estado == 0), deshabilita el botón de editar y cambia el botón de eliminar por actualizar
                                echo "<td class='tabla-data'>
                                        <form action='actualizarCli.php' method='post' style='display:inline;'  onsubmit='return confirmarActualizacion(\"" . $cliente->getNomCli() . "\", \"" . $cliente->getApeCli(). "\", \"" . $cliente->getDniCli() . "\")'>
                                            <input type='hidden' name='id' value='" . $cliente->getIdCli() . "'>
                                            <button type='submit' class='btn-iconos'>
                                                <img src='../static/images/actualizar.png' alt='actualizar' title='Actualizar Cliente' width='20' height='20'>
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
            echo "<br><button class='btn' style='display:block; margin-left: auto; margin-right:auto;'><a style='text-decoration:none; color:white;' href='inicioClientes.php'>Clientes</a></button>";
        }
        require '../footer.php';
    ?>

</body>
</html>
<script>
    function confirmarEliminacion(nombre, ape, dni) {
        return confirm(`¿Estás seguro que deseas dar de baja al cliente "${nombre} ${ape}" con DNI ${dni}?`);
    }
    function confirmarActualizacion(nombre, ape, dni) {
        return confirm(`¿Estás seguro que deseas volver a dar de Alta al cliente "${nombre} ${ape}" con DNI ${dni}?`);
    }
</script>