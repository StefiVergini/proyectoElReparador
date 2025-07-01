<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("../stock/stock_class.php");

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
    <title>Retirar Materiales </title>
    <link rel="stylesheet" href="../static/styles/style.css" /> 
    <link rel="stylesheet" href="../static/styles/tablas.css" />
    <script src="../static/js/funciones_empleados.js"></script>
    <script src="../static/js/funciones_select_nav.js"></script>
    <script>
        function mostrarFiltros() {

            event.preventDefault();

            var filtros = document.getElementById('filtros');
            var tablaContainer = document.querySelector('.table-container');

            if (filtros.style.display === 'none' || filtros.style.display === '') {
                filtros.style.display = 'block';
                tablaContainer.style.marginTop = '10px'; // Ajusta este valor según sea necesario
            } else {
                filtros.style.display = 'none';
            }

        }
    </script>
</head>
<body>

    <h1  class="titulo">Retirar Materiales</h1>
    <div class="div-con-botones">
            
            <button class='btn' id="btn-filtro" type="button" onclick="mostrarFiltros()" style='margin-right: 5rem;'> Filtros
                <img src='../static/images/filter.png' alt='filtro' title='Filtrar' width='23' height='23'>
            </button>      

        </div>
    </div>
    <div class="div-con-botones"  id="filtros" style="grid-column: 1 / -1; text-align:center; margin-bottom:35px; display: none;">
        <form action="" method="post">
            <div class="filtro" >
                <input class="input" type="text" name="buscar" placeholder="Buscar por ID o Descripción" id="buscar">

                <button class="btn-iconos" type="submit">
                    <img src="../static/images/lupa.png" alt="Buscar" width='30' height='20' />
                </button>
            </div>
        </form>
    </div>


    <div class="table-container">
        <form action="procesarRetiroMat.php" method="POST">
            <table class="tabla">
                <tr>
                    <th class="tabla-head">ID</th>
                    <th class="tabla-head">Descripción Artículo</th>
                    <th class="tabla-head">Cantidad</th>
                    <th class="tabla-head">Retirar</th>
                </tr>
                <?php
                    if (!empty($articulos)){
                        foreach ($articulos as $articulo): ?>
                            <tr>
                                <td><?= $articulo->getIdStock() ?></td>
                                <td><?= htmlspecialchars($articulo->getDescArt()) ?></td>
                                <td><?= $articulo->getCantidad() ?></td>
                                <td>
                                <input
                                    type="number"
                                    name="cant_egresa[<?= $articulo->getIdStock() ?>]"
                                    min="0"
                                    max="<?= $articulo->getCantidad() ?>"
                                    class="input"
                                    style="border: 1px solid black; margin:2px;"
                                >
                                </td>
                            </tr>
                        
                           
                            
                        <?php endforeach; ?>
                        
                   <?php } else {
                        echo "<tr><td class ='tabla-data' colspan='11'>No hay datos</td></tr>";
                    }
                ?>
            </table>
            <button type="submit" class="btn" style="margin:20px auto; display:block;">Retirar Materiales</button>
        </form>
          
    </div>
    <?php
        if($busquedaRealizada){
            echo "<br><button class='btn' style='display:block; margin-left: auto; margin-right:auto; margin-bottom:20px;'><a style='text-decoration:none; color:white;' href='retirarMateriales.php'>Volver a tabla Completa</a></button>";
        }
        
    ?>
    <div>
        <?php
        require '../footer.php';
        ?>
    </div>
</body>
</html>