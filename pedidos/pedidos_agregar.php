<?php
  /*Esta pagina viene desde pedidos_buscar_stock.php*/ 
  //session_start();

  require '../conexionPDO.php';
  if(count($_GET)>0){
    $stock = $_GET['id'];

    try{
      $sql = "SELECT idstock, descripcion_art, cantidad, tipo_stock, idproveedores, cuit_prov, estado_stock FROM stock WHERE idstock = $stock";
  
      $resultado=$base->prepare($sql);
      $resultado -> execute();
  
      if($resultado){
          while($row = $resultado ->fetch(PDO::FETCH_ASSOC)){
              $idStock = $row['idstock'];
              $descStock = $row['descripcion_art'];
              $cantStock = $row['cantidad'];
              $tipoStock = $row['tipo_stock'];
              $idProv = $row['idproveedores'];
              $cuitProv = $row['cuit_prov'];
              $estadoStock = $row['estado_stock'];
          }
      }
    }catch(Exception $e){
        echo $e -> getMessage();
        echo "Línea del error: " . $e->getLine();
    }finally{
        $base=null;
    }
  }else{
    echo "No se han recibido ningun ID";
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock - Agregar</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="./pedidos.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <?php
      require '../header.php';
      $idEmp = $_SESSION['id'];
    ?>

    <main>
        <h1>Pedidos - Realizar Nuevo Pedido</h1>
        <div>
          <form class='form-add-ped' action="pedidos_agregar-inter.php" method="post">
            <div class='form_input-container-pedidos'>
              <div class='input-wrap'>
                <label class='label' for="pedido_stock-id">ID Stock</label>
                <input class='input' type="number" id="pedido_stock-id" name="pedido_stock-id"" value=<?php echo $idStock ?> readonly>
              </div>

              <div class='input-wrap'>
                <label class='label' for="pedido_id-emp">ID Empleados</label>
                <input class='input' type="number" id="pedido_id-emp" name="pedido_id-emp" value =<?php echo $idEmp; ?>>
              </div>

              <div class='input-wrap'>
                <label class='label' for="pedido_cant">Cantidad</label>
                <input class='input' type="number" id="pedido_cant" name="pedido_cant">
              </div>

              <div class='input-wrap'>
                <label class='label' for="pedido_precio">Precio</label>
                <input  class='input' type="number" id="pedido_precio" name="pedido_precio">
              </div>
  
              <div class='ped-add-btn'>
                <div class='ped-add-btn'>
                  <input  class='btn' type="submit" value="Aceptar" name='pedido_agregar'>
                  <a class ='btn cancel' href="./pedidos_consulta.php">Cancelar</a>
                </div>
              </div>
            </div>
          </form>
            
          <div class='preview'>
              <h3>Datos del Articulo</h3>
              <p>
                <h6><strong>ID: </strong><?php echo $idStock; ?> </h6>
              </p>
              <p>
                <h6><strong>Descripcion: </strong> <?php echo $descStock; ?> </h6>
              </p>
              <p>
                <h6><strong>Cantidad: </strong><?php echo $cantStock; ?></h6>
              </p>
              <p>
                <h6><strong>Tipo: </strong><?php echo $tipoStock; ?></h6>
              </p>
              <p>
                <h6><strong>ID Proveedor: </strong><?php echo $idProv; ?></h6>
              </p>
              <p>
                <h6><strong>CUIT Proveedor: </strong><?php echo $cuitProv; ?></h6>
              </p>
              <p>
                <h6><strong>Estado: </strong><?php echo $estadoStock == 1?'Activo':'Inactivo'; ?></h6>
              </p>
          </div>
        </div>    
    </main>

    <?php
      require '../footer.php';
    ?>
</body>
</html>