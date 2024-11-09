<?php
  require '../conexionPDO.php';

  $busquedaRealizada = false;

  // Condicional para la busqueda en el buscador arriba de la tabla
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['stock_buscar'])) {
    $buscar = $_POST['stock_buscar'];
    $sql = "SELECT * FROM stock WHERE idstock LIKE '$buscar%' OR descripcion_art LIKE '$buscar%' OR tipo_stock LIKE '$buscar%' OR idproveedores LIKE '$buscar%'";
    $busquedaRealizada = true;
  }else{
    $sql = "SELECT * FROM stock";
    $busquedaRealizada = true;
  }

  try{
    $resultado=$base->prepare($sql);
    $resultado -> execute();
  }catch(Exception $e){
      echo $e-> getMessage(); 
      echo "Línea del error: " . $e->getLine();
  }finally{
      $base=null;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock - Consultas</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="./stock.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    
    <?php
      require '../header.php';
    ?>

    <main>
      <h1>Stock - Consultas</h1>
      <div class='principal-actions'>
        <a class='btn' href="stock_buscar-provider.php">Agregar Articulo</a>
        <form target='_blank' action='./stock_pdf.php' method ='POST' enctype='multipart/form-data'>
          <input class='btn stock-report' type="submit" name='report_pdf' value='Generar Reporte PDF'>
        </form>
        <a class='btn excel' href="./stock_excel.php">Generar Excel</a>
      </div>
        
      <div class='table-container'>
        <table>
          <caption class='stock-search'>
            <form action="" method='POST'>
              <label class='stock_search-label' for="stock_buscar">Busque su pedido aqui por ID, Descripcion o Tipo</label>
              <input class='input' type="search" name="stock_buscar" id="stock_buscar" placeholder="Busque su pedido aqui...">
              <input class='btn btn-search' type="submit" name='search' value='Buscar'>
            </form>
          </caption>
          <!--<colgroup>
            <col class="stock_id-art">
            <col class="stock_desc">
            <col class="stock_cant">
            <col class="stock_id-prov">
            <col class="stock_tipo">
            <col class="stock_actions">
          </colgroup>-->
          <thead>
            <tr>
              <th>ID Articulo</th>
              <th>Descripcion</th>
              <th>Cantidad</th>
              <th>ID Proveedor</th>
              <th>Tipo</th>
              <th>Estado</th>
              <th>Editar/Eliminar</th>
            </tr>
          </thead>

          <tbody>
            <?php if($resultado){
              while($row = $resultado ->fetch(PDO::FETCH_ASSOC)){?>
              <tr>
                <td><?php echo $row['idstock']?></td>
                <td><?php echo $row['descripcion_art'] ?></td>
                <td><?php echo $row['cantidad'] ?></td>
                <td><?php echo $row['idproveedores'] ?></td>
                <td><?php echo $row['tipo_stock'] ?></td>
                <td><?php echo $row['estado_stock'] ?></td>
                <td>
                  <?php if($row['estado_stock'] === 1){ ?>
                    <a href="./stock-edit.php?id=<?php echo $row['idstock'];?>&desc=<?php echo $row['descripcion_art'];?>&cant=<?php echo $row['cantidad'];?>&tipo=<?php echo $row['tipo_stock'];?>&idProv=<?php echo $row['idproveedores'] ?>;">
                      <img src='../static/images/editar.png' alt='modificar' title='Editar Stock' width='20' height='20'>
                    </a>

                    <a href="./stock_delete.php?id=<?php echo $row['idstock'];?>" onclick="return confirmDelete('Estas a punto de eliminar el archivo: ID: <?php echo $row['idstock']; ?> - <?php echo $row['descripcion_art']; ?> ')">
                      <img src='../static/images/borrar.png' alt='eliminar' title='Eliminar Stock' width='20' height='20'>
                    </a>
                  <?php }else{ ?>
                    <a href="./actualizar_stock.php?id=<?php echo $row['idstock'];?>">
                      <img src='../static/images/actualizar.png' alt='actualizar' title='Actualizar Stock' width='20' height='20'>
                    </a>
                  <?php } ?>
        
                </td>
              </tr>

              <?php }?>
            <?php }?>

          </tbody>
        </table>
      </div>
    </main>
    <?php
      require '../footer.php';
    ?>

    <script src='../static/js/confirms_alerts.js'></script>
</body>
</html>