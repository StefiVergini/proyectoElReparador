<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("../proveedores/proveedores_class.php");
    
    $proveedores = new Proveedores($base);
    $pedidos = $proveedores->obtenerTodosLosPedidos();
    $contador = 0;

    $busquedaRealizada = false;
    
    $pedidos_filtro = [];
    // Manejar la búsqueda
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $desde = $_POST['desde'] ?? '';
        $hasta = $_POST['hasta'] ?? '';
        $filtro_proveedor = $_POST['filtro_proveedor'] ?? '';
    
        // Si se quiere filtrar por fechas, ambas deben estar presentes
        if ((!empty($desde) && !empty($hasta)) || !empty($filtro_proveedor)) {
            $pedidos_filtro = $proveedores->filtrarPedidos($desde, $hasta, $filtro_proveedor);
            $busquedaRealizada = true;
        } else {
            $pedidos_filtro = $proveedores->obtenerTodosLosPedidos();
            if ((!empty($desde) && empty($hasta)) || (empty($desde) && !empty($hasta))) {
                $error = "Si deseas filtrar por fechas, debes ingresar ambas.";
            }
        }
    } else {
        $pedidos_filtro = $proveedores->obtenerTodosLosPedidos();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos Finalizados</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/card.css" /> 
    <script src="../static/js/funciones_empleados.js"></script>
    <script src="../static/js/funciones_select_nav.js"></script>
    <script>
        function mostrarFiltros() {

            event.preventDefault();

            var filtros = document.getElementById('filtros');
            filtros.classList.toggle('mostrar');
            var gridContainer = document.querySelector('.grid-container');

            if (filtros.style.display === 'none' || filtros.style.display === '') {
                filtros.style.display = 'block';
                gridContainer.style.marginTop = '90px'; 
            } else {
                filtros.style.display = 'none';
            }

        }
    </script>

</head>
<body>
    <h1 class="titulo">Pedidos Finalizados</h1>
    <div class="div-con-botones">
        <form action="nuevoPedido.php" method="post">
            <button  class='btn'> Nuevo Pedido 
                <a href="nuevoPedido.php"></a>
            </button>
        </form>
        <form action="pedidosActivos.php" method="post">
            <button class='btn'> Pedidos Activos
                <a href="pedidosActivos.php"></a>
            </button>
        </form>
        <button class='btn' id="btn-filtro" type="button" onclick="mostrarFiltros()"> Filtros
            <img src='../static/images/filter.png' alt='filtro' title='Filtrar' width='23' height='23'>
        </button>
    </div>
    <div class="div-con-botones" id="filtros" style="display: none; grid-column: 1 / -1; text-align:center;">
        <form action="" method="post">
            <div  class="form-group">
                <h2>Filtros: </h2>
                <div id="desde" style="display: inline-block;">
                    <label class="label" for="desde">Fecha Desde: </label>
                    <input class="input" type="date" name="desde"><br>
                </div>

                <div id="hasta" style="display: inline-block;">
                    <label class="label" for="hasta">Fecha Hasta: </label>
                    <input class="input" type="date" name="hasta"><br>
                </div>

                <div id="filtro_proveedor"> 
                    <select class="input" name="filtro_proveedor">
                        <option value="">Filtrar Proveedor</option>
                        <?php
                        $prov = $proveedores->leerProveedores();
                        foreach ($prov as $pro) { ?>
                            <option value="<?= $pro->getIdProv(); ?>"><?= $pro->getNomProv(); ?></option>
                        <?php } ?>
                    </select>
                    <button class="btn-iconos" type="submit">
                        <img src="../static/images/lupa.png" alt="Buscar" width='30' height='20' />
                    </button>
                </div>
            </div>
        </form>
    </div>
    <br>
    <div class="grid-container">
        <?php foreach ($pedidos_filtro as $pedido) : ?>
            <?php if ($pedido['estado_pedido'] == 0 ) : ?>
                <?php $contador++; ?>
                <?php  $fecha_pedido= date("d/m/Y", strtotime($pedido['fecha_pedido']));?>
                <?php  $fecha_ingreso= date("d/m/Y", strtotime($pedido['fecha_ingreso']));?>
                <div class="card" onclick="expandirDetalle(<?= $pedido['id_ped'] ?>, '<?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?>', '<?= date('d/m/Y', strtotime($pedido['fecha_ingreso'])) ?>')">
                    <div class="card-header">
                        Pedido #<?= $pedido['id_ped'] ?>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Fecha del Pedido:</strong> <?= $fecha_pedido?></li>
                        <li class="list-group-item"><strong>Fecha que Ingresó:</strong> <?= $fecha_ingreso ?></li>
                        <li class="list-group-item"><strong>Proveedor:</strong> <?= ucwords($pedido['nombre_prov']) ?></li>
                        <li class="list-group-item"><strong>CUIT:</strong> <?= $pedido['cuit'] ?></li>
                        <li class="list-group-item"><strong>Empleado:</strong> <?= ucwords($pedido['nom_empleado'] . " " . $pedido['ape_empleado']) ?></li>
                        <li class="list-group-item"><strong>ID Empleado:</strong> <?= $pedido['idempleados'] ?></li>
                        <li class="list-group-item" style="text-align: center;"><strong>&#9207;</strong></li>
                    </ul>
                    <br>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
        <?php if ($contador === 0 ) : ?>
            <div style="text-align: center; margin-top: 20px; margin-bottom:50px;">
                <h2><strong>No se encontraron Datos</strong></h2>
                <button class ="button blue" onclick="window.location.href='nuevoPedido.php'">Nuevo Pedido</button>
                <button class ="button red" onclick="window.location.href='historialPedidos.php'">Volver</button>
            </div>
        <?php endif; ?>
        
    <!-- Overlay para la tarjeta expandida -->
    <div class="overlay" id="overlay">
        <div class="expanded-card" id="detalle-expandido">
            <button class="close-btn" onclick="cerrarDetalle()">X</button>
            <div id="contenido-pedido"></div>
        </div>
    </div>

    <?php
        if($busquedaRealizada){
            echo "<br><button class='btn' style='display:block; margin-left: auto; margin-right:auto; margin-bottom: 15px;'><a style='text-decoration:none; color:white;' href='historialPedidos.php'>Volver</a></button>";
        }
        require '../footer.php'; ?>

    <script>
        function expandirDetalle(id, fechaPedido, fechaIngreso) {
            let overlay = document.getElementById("overlay");
            let contenido = document.getElementById("contenido-pedido");

            // Obtener los datos del pedido seleccionado
            <?php foreach ($pedidos as $pedido) : ?>
                if (id === <?= $pedido['id_ped'] ?>) {
                    contenido.innerHTML = `
                   
                        <div class="card-header">
                            Pedido #<?= $pedido['id_ped'] ?>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Fecha Pedido:</strong> ${fechaPedido}</li>
                            <li class="list-group-item"><strong>Fecha que Ingresó:</strong> ${fechaIngreso}</li>
                            <li class="list-group-item"><strong>Proveedor:</strong> <?= ucwords($pedido['nombre_prov']) ?></li>
                            <li class="list-group-item"><strong>CUIT:</strong> <?= $pedido['cuit'] ?></li>
                            <li class="list-group-item"><strong>Empleado:</strong> <?= ucwords($pedido['nom_empleado'] . " " . $pedido['ape_empleado']) ?></li>
                            <li class="list-group-item"><strong>ID Empleado:</strong> <?= $pedido['idempleados'] ?></li>
                        </ul>
                        <div class="card-header">
                            Detalle Pedido:
                        </div>
                        <table>
                            <tr>
                                <th>Artículo</th>
                                <th>Cantidad Pedida</th>
                                <th>Cantidad que Ingresó</th>
                            </tr>
                            <?php foreach ($pedido['articulos'] as $articulo) : ?>
                                <tr>
                                    <td><strong>-<?= ucwords($articulo['descripcion_art']) ?></strong></td>
                                    <td><?= $articulo['cant_pedida'] ?>.-</td>
                                    <td><?= $articulo['cant_ingresa'] ?>.-</td>
                                </tr>
                                
                            <?php endforeach; ?>
                            
                        </table>

                        <div class="card-header">
                            &#10003;
                        </div>

                    `;
                }
            <?php endforeach; ?>

            overlay.style.display = "flex";  // Mostrar el overlay centrado
        }

        function cerrarDetalle() {
            document.getElementById("overlay").style.display = "none";
        }
    </script>

    
</body>
</html>