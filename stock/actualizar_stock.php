<?php
    /*Esto viene desde stock_Consutas.php al presionar 'actualizar' */
    require '../conexionPDO.php';

    if(isset($_GET['id'])){
        $idStock = $_GET['id'];

        try{
            $sql = "UPDATE stock SET estado_stock = 1 WHERE idstock = $idStock";
        
            $resultado=$base->prepare($sql);
            $resultado -> execute();
          }catch(Exception $e){
              echo $e-> getMessage(); 
              echo "Línea del error: " . $e->getLine();
          }finally{
              $base=null;
          }
    }else{
        echo "No se recibieron datos.";
    }

    header('Location: ./stock_Consultas.php');
?>