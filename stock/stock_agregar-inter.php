<?php
    /*Esto viene de stock_agregar.php al darle submit al formulario*/
    require '../conexionPDO.php';
    require '../test_input.php';

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(empty($_POST['stock_prov-cuit'])){
            echo"El cuit del proveedor no puede ser vacio.";
        }else{
            $cuitProv = test_input($_POST['stock_prov-cuit']);
        }

        if(empty($_POST['stock_prov-id'])){
            echo"El id del proveedor no puede ser vacio.";
        }else{
            $idProv = test_input($_POST['stock_prov-id']);
        }

        if(empty($_POST['stock_agre-desc'])){
            echo"La descripcion del articulo no puede ser vacio.";
        }else{
            $desc = test_input($_POST['stock_agre-desc']);
        }

        $cant = $_POST['stock_agre-cant'];

        if(empty($_POST['stock_agre-tipo'])){
            echo"El tipo del articulo no puede ser vacio.";
        }else{
            $tipo = test_input($_POST['stock_agre-tipo']);
        }
        
        try{
            $sql = "INSERT INTO stock(descripcion_art, cantidad, tipo_stock, idproveedores,cuit_prov) VALUES ('$desc', $cant, '$tipo', $idProv, '$cuitProv')";

            $resultado=$base->prepare($sql);
            $resultado -> execute();
        }catch(Exception $e){
          echo "Línea del error: " . $e->getLine();
          echo "Error: ".$e-> getMessage();
        }finally{
            $base=null;
        }
    }else{
        echo "No se han recibido datos";
    }
    header('Location: ./stock_Consultas.php');
?>