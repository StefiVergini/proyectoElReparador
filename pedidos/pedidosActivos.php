<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("../proveedores/proveedores_class.php");
    
    $proveedores = new Proveedores($base);
    $pedidos = $proveedores->obtenerTodosLosPedidos();
    $contador = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos Activos</title>
    <link rel="stylesheet" href="../static/styles/style.css" /> 
    <link rel="stylesheet" href="../static/styles/card.css" /> 
    <script src="../static/js/funciones_empleados.js"></script>
    <script src="../static/js/funciones_select_nav.js"></script>

</head>
<body>
    <h1 class="titulo">Pedidos Activos</h1>
    <div class="div-con-botones">
        <form action="nuevoPedido.php" method="post">
            <button  class='btn'> Nuevo Pedido 
                <a  href="nuevoPedido.php"></a>
            </button>
        </form>
        <form action="historialPedidos.php" method="post">
            <button class='btn'> Historial de Pedidos
                <a  href="historialPedidos.php"></a>
            </button>
        </form>
    </div>
        <div class="grid-container">
            <?php foreach ($pedidos as $pedido) : ?>
                <?php if ($pedido['estado_pedido'] == 1 ) : ?>
                    <?php $contador++; ?>
                    <?php  $fecha_pedido= date("d/m/Y", strtotime($pedido['fecha_pedido']));?>
                    <div class="card" onclick="expandirDetalle(<?= $pedido['id_ped'] ?>)">
                        <div class="card-header">
                            Pedido #<?= $pedido['id_ped'] ?>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Fecha:</strong> <?= $fecha_pedido?></li>
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
                <h2><strong>No hay pedidos activos en este momento</strong></h2>
                <!-- <button class ="button blue" onclick="window.location.href='nuevoPedido.php'">Nuevo Pedido</button>
                <button class ="button red" onclick="window.location.href='historialPedidos.php'">Historial de Pedidos</button>-->
            </div>
        <?php endif; ?>
        
    <!-- Overlay para la tarjeta expandida -->
    <div class="overlay" id="overlay">
        <div class="expanded-card" id="detalle-expandido">
            <button class="close-btn" onclick="cerrarDetalle()">X</button>
            <div id="contenido-pedido"></div>
        </div>
    </div>

    <?php require '../footer.php'; ?>

    <script>
        function expandirDetalle(id) {
            let overlay = document.getElementById("overlay");
            let contenido = document.getElementById("contenido-pedido");

            // Obtener los datos del pedido seleccionado
            <?php foreach ($pedidos as $pedido) : ?>
                if (id === <?= $pedido['id_ped'] ?>) {
                    <?php  $fecha_pedido= date("d/m/Y", strtotime($pedido['fecha_pedido']));?>
                    contenido.innerHTML = `
                       
                        <div class="card-header">
                            Pedido #<?= $pedido['id_ped'] ?>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Fecha:</strong> <?= $fecha_pedido ?></li>
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
                                <th>Cantidad</th>
                            </tr>
                            <?php foreach ($pedido['articulos'] as $articulo) : ?>
                                <tr>
                                    <td><strong>-<?= ucwords($articulo['descripcion_art']) ?></strong></td>
                                    <td><?= $articulo['cant_pedida'] ?>.-</td>
                                </tr>
                                
                            <?php endforeach; ?>
                            
                        </table>

                        <form action="finalizarPedido.php" method="POST">
                            <div class="card-header">
                                Finalizar Pedido:
                            </div>
                            <input type="hidden" name="id_ped" value="<?= $pedido['id_ped'] ?>">
                            <input type="hidden" name="fecha_pedido" value="<?= $pedido['fecha_pedido'] ?>">
                            <ul class="list-group" style="background:#fdf1b7;">
                                <li class="list-group-item centered">
                                    <strong><label class="label" for="fecha_fin">Fecha que Ingresa:<input class="input" type="date" name="fecha_fin" required></label></strong>
                                    
                                </li>
                            
                                <table>
                                <tr>
                                    <th>Artículo</th>
                                    <th>Cantidad Pedida</th>
                                    <th>Cantidad Ingresa</th>
                                </tr>
                                <?php foreach ($pedido['articulos'] as $articulo) : ?>
                                    <tr>
                                        <td><strong>-<?= ucwords($articulo['descripcion_art']) ?></strong></td>
                                        <td><?= $articulo['cant_pedida'] ?>.-</td>
                                        <td><input class="input" type="number" name="cant_ingresa[<?= $articulo['idstock'] ?>]" required></td>
                                    </tr>
                                        
                                    
                                <?php endforeach; ?>
                                </table>

                            
                                <li class="list-group-item centered" ><button class="btn" type="submit">Finalizar</button></li> 
                            </ul>   
                        </form>
                    
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
