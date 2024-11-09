<?php
  require '../conexionPDO.php';
  require '../test_input.php';

  if(!empty($_GET)){
    $idStock = $_GET['id'];
    $descStock = $_GET['desc'];
    $cantStock = $_GET['cant'];
    $tipoStock = $_GET['tipo'];
    $idProv = $_GET['idProv'];

    try{
      $sql = "SELECT * FROM proveedores WHERE idproveedores=$idProv";

      $resultado= $base->prepare($sql);
      $resultado -> execute();

      if($resultado){
        while($row = $resultado ->fetch(PDO::FETCH_ASSOC)){
            $idProv = $row['idproveedores'];
            $cuitProv = $row['cuit'];
            $nombreProv = $row['nombre_prov'];
            $telProv = $row['tel_prov'];
            $dirProv = $row['dir_prov'];
            $emailProv = $row['email_prov'];
        }
      }
    }catch(Exception $e){
      echo $e-> getMessage(); 
      echo "Línea del error: " . $e->getLine();
    }finally{
        $base=null;
    }
  }else{
    echo "No se recibio ninguna informacion.";
  }
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock - Modificar</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="./stock.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <?php
      require '../header.php';
    ?>

    <main>
        <div>
            <h1>Stock - Modificar Articulo</h1>
        </div>

        <section>
            <form class='form-edit-stock' action="stock-edit_inter.php" method="post">
                <div class='form_input-container'>
                  <div class='stock_inputs-edit-type'>
                    <div>
                      <label class='label stock-label-1' for="stock-id">ID Stock</label>
                      <input class='input stock-input-1' type="number" id="stock-id" name="stock-id" value=<?php echo $idStock ?> readonly>
                    </div>
                    
                    <div>
                      <label class='label stock-label-2' for='stock_edit-cuitProv'>Cuit Proveedor</label>
                      <input class='input stock-input-2' type="text" name='stock_edit-cuitProv' value=<?php echo $cuitProv;?> readonly>
                    </div>
                  </div>

                  <div class='stock_inputs-edit-type'>
                    <div>
                      <label class='label' for="stock-idProv">ID Prov.</label>
                      <input class='input' type="text" name='stock-idProv' value=<?php echo $idProv;?>>
                    </div>

                    <div>
                      <label class='label' for="stock_edit-desc">Desc. del Articulo*</label>
                      <input class='input' id="stock_edit-desc" name="stock_edit-desc" type="text" value=<?php echo "\"".$descStock."\"" ?> required>
                    </div>
                  </div>

                  <div class='stock_inputs-edit-type'>
                    <div>
                      <label class='label' for="stock_edit-cant">Cant.*</label>
                      <input class='input' id="stock_edit-cant" name="stock_edit-cant" type="number" value=<?php echo $cantStock; ?> required>
                    </div>
                    
                    <div>
                      <label class='label' for="stock_edit-tipo">Tipo del Articulo*</label>
                      <select class='input' name="stock_edit-tipo" id="stock_edit-tipo">
                          <option value=<?php echo $tipoStock;?> selected>ELIJA UNA OPCION</option>
                          <option value="Herramientas">Herramientas</option>
                          <option value="Electronica">Electronica</option>
                          <option value="Insumos">Insumos</option>
                          <option value="Libreria">Libreria</option>
                          <option value="Otros">Otros</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class='preview'>
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
                </div>

                <div class='flex-container'>
                  <input class='btn' type="submit" value="Aceptar">
                  <a class='btn cancel' type='button' href="stock_Consultas.php">Cancelar</a>
                </div>
            </form>
        </section>
    </main>
    <?php
      require '../footer.php';
    ?>
</body>
</html>