<?php
    require '../conexionPDO.php';
    if(isset($_GET['id'])){
        $idStock = $_GET['id'];
        try{
            $sql = "UPDATE stock SET estado_stock = 0 WHERE idstock = $idStock";
            $resultado=$base->prepare($sql);
            $resultado -> execute();
            $resultado->closeCursor();

            header('Location: stock_Consultas.php');
        }catch(Exception $e){
            echo "Línea del error: " . $e->getLine();
            /*echo "<a href='http://localhost/ifts/static/stock_Consultas.php'>Regresar a Consultas Stock</a>"*/
        }finally{
            $base=null;
        }
    }
?>