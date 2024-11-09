<?php
  /*Esta pagina viene desde stock_buscar-provider.php */
  require '../conexionPDO.php';
  require '../test_input.php';

  $count = 0;

  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(empty($_POST['stock_agre-prov']) || $_POST['stock_agre-prov']==0){
      echo "El id del proveedor no puede ser vacia.";
      header('Location: ./stock_Consultas.php');
    }else{
      $provider = test_input($_POST['stock_agre-prov']);
      try{
        $sql = "SELECT idproveedores, cuit, nombre_prov, tel_prov, dir_prov, email_prov FROM proveedores WHERE cuit=$provider";
  
        $resultado=$base->prepare($sql);
        $resultado -> execute();
  
        $count = $resultado ->rowCount();
        //echo $count;
  
        if($count > 0){
          while($row = $resultado ->fetch(PDO::FETCH_ASSOC)){
            $idProv = ($row['idproveedores']);
            $cuitProv = $row['cuit'];
            $nombreProv = $row['nombre_prov'];
            $telProv = $row['tel_prov'];
            $dirProv = $row['dir_prov'];
            $emailProv = $row['email_prov'];
          }
        }
      }catch(Exception $e){
        echo "Línea del error: " . $e->getLine();
        echo "Error: ".$e-> getMessage();
      }finally{
          $base=null;
      }
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
    <title>Stock - Agregar</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="./stock.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <?php
      require '../header.php';
    ?>

    <main>
        <h1>Stock - Agregar Articulo</h1>
        <div>
          <?php if($count === 0){ 
            echo "<p>No se ha encontrado ningun proveedor registrado con el ID/CUIT solicitado.</p>";
           }else{ ?>

            <form class='form-edit-stock' action="stock_agregar-inter.php" method="post">
              <div class='form_input-container'>
                <div class='stock_inputs-id-cuit'>
                  <div class='flex-inputs'>
                    <label class='label' for="stock_prov-cuit">CUIT/DNI Proveedor</label>
                    <input class='input' type="text" id="stock_prov-cuit" name="stock_prov-cuit" value=<?php echo $cuitProv;?> required> 
                  </div>
                  <div class='flex-inputs'>
                    <label class='label' for="stock_prov-id">ID Proveedor</label>
                    <input class='input' type="number" id="stock_prov-id" name="stock_prov-id" value=<?php echo $idProv; ?> readonly required>
                  </div>
                </div>

                <div  class='stock_inputs-id-cuit'>
                  <div class='flex-inputs'>
                    <label class='label' for="stock_agre-desc">Descripcion del Articulo</label>
                    <input class='input' type="text" id="stock_agre-desc" name="stock_agre-desc">
                  </div>
                  <div class='flex-inputs'>
                    <label class='label' for="stock_agre-cant">Cantidad</label>
                    <input class='input' type="number" id="stock_agre-cant" name="stock_agre-cant">
                  </div>
                </div>

                <div class='stock_inputs-id-cuit'>
                  <div class='flex-inputs'>
                    <label class='label' for="stock_agre-tipo">Tipo del Articulo</label>
                    <select class='input' name="stock_agre-tipo" id="stock_agre-tipo" required>
                      <option value="Herramientas">Herramientas</option>
                      <option value="Electronica">Electronica</option>
                      <option value="Insumos">Insumos</option>
                      <option value="Libreria">Libreria</option>
                      <option value="Otros">Otros</option>
                    </select>
                  </div>   
                </div>

                <div class='stock_add-btn'>
                  <div class='stock_add-btn'>
                    <input class='btn' type="submit" value="Aceptar" name='stock_save-art'>
                    <a class='btn cancel' href="stock_Consultas.php">Cancelar</a>
                  </div>
                </div>
              </div>
              
            </form>
           <?php }?>

          <div class='preview'>
            <?php if($count === 0){ 
              echo "<p>No se ha encontrado ningun proveedor registrado con el ID/CUIT solicitado.</p>";
            }else{ ?>
              <h3>Datos del Proveedor</h3>
                  <p>
                    <h6><strong>ID: </strong><?php echo $idProv; ?></h6>
                  </p>
                  <p>
                    <h6><strong>CUIT: </strong><?php echo $cuitProv; ?></h6>
                  </p>
                  <p>
                    <h6><strong>Tel.: </strong><?php echo $telProv; ?></h6>
                  </p>
                  <p>
                      <h6><strong>Nombre/Razon Social: </strong><?php echo $nombreProv; ?></h6>
                  </p>
                  <p>
                    <h6><strong>Email: </strong><?php echo $emailProv; ?> </h6>
                  </p>
                  <p>
                    <h6><strong>Direccion: </strong><?php echo $dirProv; ?> </h6>
                  </p>
            <?php } ?>
          </div>
        </div>    
    </main>
    <?php
      require '../footer.php';
    ?>
</body>
</html>