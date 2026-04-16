<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("stock_class.php");

    $busquedaRealizada = false;
    
    $stockModel = new Stock($base);


    $articulos = [];
    // Manejar la búsqueda
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!empty($_POST['buscar']) || !empty($_POST['filtro_cantidad']) || !empty($_POST['filtro_proveedor']))) {
        $buscar = $_POST['buscar'] ?? '';
        $filtro_cantidad = $_POST['filtro_cantidad'] ?? '';
        $filtro_proveedor = $_POST['filtro_proveedor'] ?? '';
    
        // Solo ejecutar la búsqueda si al menos un filtro tiene valor
        if (!empty($buscar) || !empty($filtro_cantidad) || !empty($filtro_proveedor)) {
            $articulos = $stockModel->buscarStock($buscar, $filtro_cantidad, $filtro_proveedor);
            $busquedaRealizada = true;
        } else {
            $articulos = $stockModel->leerArticulos();
        }
    } else {
        $articulos = $stockModel->leerArticulos();
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artículos | Stock </title>
    <link rel="stylesheet" href="../static/styles/style.css" /> 
    <link rel="stylesheet" href="../static/styles/tablas.css" />
    <script src="../static/js/funciones_empleados.js"></script>
    <script src="../static/js/funciones_select_nav.js"></script>
    <script>
        function mostrarFiltros() {

            event.preventDefault();

            var filtros = document.getElementById('filtros');
            filtros.classList.toggle('mostrar');
            var tablaContainer = document.querySelector('.table-container');

            if (filtros.style.display === 'none' || filtros.style.display === '') {
                filtros.style.display = 'block';
                tablaContainer.style.marginTop = '50px'; // Ajusta este valor según sea necesario
            } else {
                filtros.style.display = 'none';
            }

        }
    </script>
</head>
<body>

    <h1  class="titulo">Artículos - Stock</h1>
    <div class="div-con-botones">
        
            <div class="desplegable" style="margin-right: 5rem;">
                <button class="btn">Exportar &#9207;</button>
                <div class="link">
                    <a target="_blank" href="stock_pdf.php">PDF</a>
                    <a class="excel" href="stock_excel.php">Excel</a>
                </div>
            </div>
            
            <form action='altaStock.php' method='post'  style='margin-right: 5rem;'>
                <button type='submit' class='btn'>
                   Agregar +
                </button>
            </form>

            <button class='btn' id="btn-filtro" type="button" onclick="mostrarFiltros()" style='margin-right: 5rem;'> Filtros
                <img src='../static/images/filter.png' alt='filtro' title='Filtrar' width='23' height='23'>
            </button>
            <div class="desplegable" style="margin-right: 5rem;">
                <button class="btn">Pedidos &#9207;</button>
                <div class="link">
                    <a href="../pedidos/nuevoPedido.php">Nuevo Pedido</a>
                    <a href="../pedidos/pedidosActivos.php">Pedidos Activos</a>
                    <a href="../pedidos/historialPedidos.php">Historial de Pedidos</a>

                </div>
            </div>
       

        </div>
    </div>
    <div class="filtro " id="filtros" style="display:none;">
        <form action="" method="post">
            <div class="div-con-botones" >
                <div class="form-group">
                    <input class="input" type="text" name="buscar" placeholder="Buscar por ID o Descripción" id="buscar">

                    <select class="input" name="filtro_cantidad">
                        <option value="">Filtrar por cantidad</option>
                        <option value="sinArt">Sin Unidades</option>
                        <option value="menor10">Menor a 10 Unidades</option>
                        <option value="mayor10">Mayor a 10 Unidades</option>
                    </select>

                    <select class="input" name="filtro_proveedor">
                        <option value="">Filtrar por proveedor</option>
                        <?php
                        $proveedores = $stockModel->leerProv();
                        foreach ($proveedores as $prov) {?>
                            <option value="<?= $prov->getIdProv(); ?>"><?= $prov->getNomProv(); ?></option>
                        <?php }
                        ?>
                    </select>

                    <button class="btn-iconos" type="submit">
                        <img src="../static/images/lupa.png" alt="Buscar" width='30' height='20' />
                    </button>
                </div>
            </div>
        </form>
    </div>


    <div class="table-container">

        <table class="tabla">
            <tr>
                <th class="tabla-head">ID</th>
                <th class="tabla-head">Descripción Artículo</th>
                <th class="tabla-head">Cantidad</th>
                <th class="tabla-head">Tipo</th>
                <th class="tabla-head">ID Proveedor</th>
                <th class="tabla-head">Nombre Proveedor</th>
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
                if (!empty($articulos)){
                    foreach ($articulos as $articulo) {
                        echo "<tr>";
                        echo "<td class='tabla-data'>" . $articulo->getIdStock() . "</td>";
                        echo "<td class='tabla-data'>" . $articulo->getDescArt() . "</td>";
                        echo "<td class='tabla-data'>" . $articulo->getCantidad() . "</td>";
                        echo "<td class='tabla-data'>" . $articulo->getTipoStock() . "</td>";
                        echo "<td class='tabla-data'>" . $articulo->getIdProv() . "</td>";
                        echo "<td class='tabla-data'>" . $articulo->getNomProv() . "</td>";
                        echo "<td class='tabla-data'>" . ($articulo->getEstadoStock() == 1 ? 'Activo' : 'Inactivo') . "</td>";
                        if($rol == 2 || $rol == 4 || $rol == 5 || $rol == 6){
                            
                            echo "<td class='tabla-data'>
                                    <form action='modificarStock.php' method='post' style='display:inline;'>
                                        <input type='hidden' name='id' value='" . $articulo->getIdStock() . "'>
                                        <button type='submit' class='btn-iconos'>
                                            <img src='../static/images/editar.png' alt='modificar' title='Editar Proveedor' width='20' height='20'>
                                        </button>
                                    </form>
                                    |   
                                    <form action='eliminarStock.php' method='post' style='display:inline;' 
                                        onsubmit='return confirmarEliminacion(\"" . addslashes($articulo->getDescArt()) . "\")'>
                                        <input type='hidden' name='id' value='" . $articulo->getIdStock() . "'>
                                        <button type='submit' class='btn-iconos'>
                                            <img src='../static/images/borrar.png' alt='eliminar' title='Eliminar Artículo' width='20' height='20'>
                                        </button>
                                    </form>
                                </td>";
                            
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
            echo "<br><button class='btn' style='display:block; margin-left: auto; margin-right:auto; margin-bottom:20px;'><a style='text-decoration:none; color:white;' href='inicioStock.php'>Articulos | Stock</a></button>";
        }
        require '../footer.php';
    ?>

</body>
</html>
<script>
    function confirmarEliminacion(nombre) {
        return confirm(`¿Estás seguro de que deseas eliminar el artículo "${nombre}"?`);
    }
</script>


