<?php
    require '../conexionPDO.php';

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $idStock = $_POST['pedido-id-stock'];
        $idEmp = $_POST['pedido_idEmp'];
        $cantPed = $_POST['pedido_cant'];
        $precioPed = $_POST['pedido_precio'];

        try{
            $sql = "UPDATE pedidos SET cantidad_pedido = $cantPed, precio_pedido = $precioPed WHERE id_stock = $idStock AND idempleados = $idEmp";

            $resultado=$base->prepare($sql);
            $resultado -> execute();

            /*$resultado->closeCursor();*/

            header('Location: pedidos_consulta.php');

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