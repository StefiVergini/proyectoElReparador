<?php
    require '../conexionPDO.php';
    require '../test_input.php';

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(empty($_POST['stock-id'])){
            echo "El id del articulo no puede ser vacia.";
        }else{
            $idStock = test_input($_POST['stock-id']);
        }

        if(empty($_POST['stock_edit-cuitProv'])){
            echo "El cuit del proveedor no puede ser vacia.";
        }else{
            $cuitProv = test_input($_POST['stock_edit-cuitProv']);
        }

        if(empty($_POST['stock-idProv'])){
            echo "El id del proveedor no puede ser vacia.";
        }else{
            $idProv = test_input($_POST['stock-idProv']);
        }
        
        if(empty($_POST['stock_edit-desc'])){
            echo "La descripcion del articulo no puede ser vacia.";
        }else{
            $desc = test_input($_POST['stock_edit-desc']);
        }

        $cant = test_input($_POST['stock_edit-cant']);

        if(empty($_POST['stock_edit-tipo'])){
            echo "El tipo del articulo no puede ser vacia.";
        }else{
            $tipo = test_input($_POST['stock_edit-tipo']);
        }
        
        try{
            $sql = "UPDATE stock SET descripcion_art = '$desc', cantidad = $cant, tipo_stock = '$tipo', idproveedores = $idProv WHERE idstock = $idStock";

            $resultado=$base->prepare($sql);
            $resultado -> execute();

            echo "Registro insertado";
                
            $resultado->closeCursor();

            header('Location: ./stock_Consultas.php');

            echo "<a href='http://localhost/ifts/static/stock/stock_Consultas.php'>Regresar a Consultas Stock</a>";

        }catch(Exception $e){
            echo "Línea del error: " . $e->getLine();
            echo "<a href='http://localhost/ifts/static/stock/stock_Consultas.php'>Regresar a Consultas Stock</a>";
        }finally{
            $base=null;
        }
    }else{
        echo "No se ha recibido ninguna informacion desde el formulario de edicion.";
        echo "<a href='http://localhost/ifts/static/stock/stock_Consultas.php'>Regresar a Consultas Stock</a>";
    }  
?>