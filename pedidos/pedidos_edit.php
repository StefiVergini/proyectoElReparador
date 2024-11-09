<?php
    require '../conexionPDO.php';

    if(isset($_GET['id'])){
        $idStock = $_GET['id'];
        
        try{
            $sql = "SELECT * FROM pedidos WHERE id_stock = $idStock";

            $resultado=$base->prepare($sql);
            $resultado -> execute();

            /*$resultado->closeCursor();*/

            if($resultado){
              while($row = $resultado ->fetch(PDO::FETCH_ASSOC)){
                  $idStock = $row['id_stock'];
                  $idEmp = $row['idempleados'];
                  $fechPed = $row['fecha_pedido'];
                  $fechIngPed = $row['fecha_ingreso'];
                  $cantPed = $row['cantidad_pedido'];
                  $precioPed = $row['precio_pedido'];
                  $estadoPed = $row['estado_pedido'];
              }
            }
        }catch(Exception $e){
            echo "Línea del error: " . $e->getLine();
            echo "Error: ".$e-> getMessage();
        }finally{
            $base=null;
        }
    }else{
        echo "No se han recibido datos";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock - Modificar</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <?php
      require '../header.php';
    ?>

    <main>
        <div>
            <h1>Pedidos - Modificar Pedido</h1>
        </div>
        <section>
            <form action="pedidos_edit_inter.php" method="post">
                <div>
                    <label for="pedido-id-stock">ID Stock</label>
                    <input type="number" id="pedido-id-stock" name="pedido-id-stock" value=<?php echo $idStock; ?> readonly>

                    <label for='pedido_idEmp'>ID Empleados</label>
                    <input type="text" name='pedido_idEmp' value=<?php echo $idEmp;?> readonly>

                    <label for='pedido_cant'>Cantidad</label>
                    <input type="number" name='pedido_cant' value=<?php echo $cantPed;?>>

                    <label for='pedido_precio'>Precio</label>
                    <input type="number" name='pedido_precio' value=<?php echo $precioPed;?>>
                </div>

                <div>
                  <h3>Datos del Pedido</h3>
                  <p>
                    <h6>ID Stock: </h6><span><?php echo $idStock; ?></span>
                  </p>
                  <p>
                    <h6>ID Empleados: </h6><span><?php echo $idEmp; ?></span>
                  </p>
                  <p>
                    <h6>Fecha del Pedido: </h6><span><?php echo $fechPed; ?></span>
                  </p>
                  <p>
                    <h6>Fecha de Ingreso: </h6><span><?php echo $fechIngPed; ?></span>
                  </p>
                  <p>
                    <h6>Cantidad: </h6><span><?php echo $cantPed; ?></span>
                  </p>
                  <p>
                    <h6>Precio: </h6><span><?php echo $precioPed; ?></span>
                  </p>
                  <p>
                    <h6>Estado: </h6><span><?php echo $estadoPed; ?></span>
                  </p>
                </div>

                <input type="submit" value="Aceptar" name='save_edit-pedido'>
                <a href="./pedidos_consulta.php">Cancelar</a>
            </form>
        </section>
    </main>
    <?php
      require '../footer.php';
    ?>
</body>
</html>