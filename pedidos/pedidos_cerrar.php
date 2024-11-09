<?php
    require '../conexionPDO.php';
    require '../test_input.php';
    //date_default_timezone_set('America/Argentina/Buenos_Aires');

    if(isset($_GET['id'])){
        $idPed = $_GET['id'];

        try{
            $sql = "SELECT pedidos.id_ped, pedidos.id_stock, pedidos.idempleados, pedidos.fecha_pedido, pedidos.fecha_ingreso, pedidos.cantidad_pedido, pedidos.precio_pedido, pedidos.estado_pedido, stock.idstock, stock.descripcion_art FROM pedidos INNER JOIN stock ON pedidos.id_stock = stock.idstock WHERE pedidos.id_ped = $idPed";

            $resultado=$base->prepare($sql);
            $resultado -> execute();

            /*$resultado->closeCursor();*/

            if($resultado){
              while($row = $resultado ->fetch(PDO::FETCH_ASSOC)){
                  $idPed = $row['id_ped'];
                  $idStock = $row['id_stock'];
                  $idEmp = $row['idempleados'];
                  $fechPed = $row['fecha_pedido'];
                  $desc = $row['descripcion_art'];
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
        echo "No se han recibido datos del GET";  
    }

    if(isset($_POST['cerrar_pedido'])){
        require '../conexionPDO.php';
        $fechIngPed = new DateTime($_POST['fecha_ing']);
        $fechasql = $fechIngPed -> format('Y-m-d');
        var_dump($fechasql);
        
        try{
            $sql = "UPDATE pedidos SET fecha_ingreso = '$fechasql', estado_pedido = 0 WHERE id_ped = $idPed AND id_stock = $idStock";

            $resultado=$base->prepare($sql);
            $resultado -> execute();

            header('Location: ./pedidos_consulta.php');

        }catch(Exception $e){
            echo "Línea del error: " . $e->getLine();
            echo "Error: ".$e-> getMessage();
        }finally{
            $base=null;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrar Pedido</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <?php
      require '../header.php';
    ?>
    <main>
        <h1>Pedidos - Finalizar un Pedido</h1>

        <div>
            <h3>Datos del Pedido</h3>
            <form class='preview'  action='' method='POST'>
                <div class='ped-close'>
                    <input class='btn cancel' type="submit" name='cerrar_pedido' value='Cerrar Pedido'>
                    <p>
                        <h6><strong>Fecha de Ingreso: </strong><input class='input' type="date" name='fecha_ing'></h6>  
                    </p>
                    <p>
                        <h6><strong>ID Pedido: </strong><?php echo $idPed; ?></h6>
                    </p>
                    <p>
                        <h6><strong>ID Stock: </strong><?php echo $idStock; ?></h6>
                    </p>
                    <p>
                        <h6><strong>ID Empleados: </strong><?php echo $idEmp; ?></h6>
                    </p>
                    <p>
                        <h6><strong>Descripcion Art: </strong><?php echo $desc; ?></h6>
                    </p>
                    <p>
                        <h6><strong>Fecha del Pedido: </strong><?php echo $fechPed; ?></h6>
                    </p>
                    <p>
                        <h6><strong>Cantidad: </strong><?php echo $cantPed; ?></h6>
                    </p>
                    <p>
                        <h6><strong>Precio: </strong><?php echo $precioPed; ?></h6>
                    </p>
                    <p>
                        <h6><strong>Estado: </strong><?php echo $estadoPed; ?></h6>
                    </p>
                </div> 
            </form>
        </div>
    </main>
    <?php
      require '../footer.php';
    ?>
</body>
</html>