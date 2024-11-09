<?php
    /* Esto viene desde pedidos_agregar.php al darle submit al form */
    require '../conexionPDO.php';
    require '../test_input.php';

    /*Seteo de variables de error en vacio. Deben setearse en el html como <span> 
    $idStockErr = $idEmp = $cant = $precio = '';*/

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
        /*Validaciones de campos vacios */
        if(empty($_POST['pedido_stock-id'])){
            echo "El id del Stock es necesario";
        }else{
            $idStock = test_input($_POST['pedido_stock-id']);
        }

        if(empty($_POST['pedido_id-emp'])){
            echo "El id del Empleado es necesario";
        }else{
            $idEmp = test_input($_POST['pedido_id-emp']);
        }

        if(empty($_POST['pedido_cant'])){
            $cant = 1;
        }else{
            $cant = test_input($_POST['pedido_cant']);
        }

        if(empty($_POST['pedido_precio']) || $_POST['pedido_precio'] === 0){
            echo "Debe especificarse un precio mayor a cero";
        }else{
            $precio = test_input($_POST['pedido_precio']);
        }

        /* Validaciones de datos numericos 
        if(!is_numeric($idStock)){
            $idStock = null;
        }
        if(!is_numeric($idEmp)){
            $idEmp = null;
        }
        if(!is_numeric($cant)){
            $cant = null;
        }
        if(!is_numeric($precio)){
            $precio = null;
        }*/
        
        try{
            $sql = "INSERT INTO pedidos(id_stock, idempleados, cantidad_pedido, precio_pedido) VALUES($idStock, $idEmp, $cant, $precio)";

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
        echo "No se han recibido dados del formulario.";
    }

?>