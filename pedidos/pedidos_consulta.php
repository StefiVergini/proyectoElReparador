<?php
  require '../conexionPDO.php';

  $busquedaRealizada = false;

  // Condicional para la busqueda en el buscador arriba de la tabla
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['pedido_search'])) {
    $buscar = $_POST['pedido_search'];
    $sql = "SELECT pedidos.id_ped, pedidos.id_stock, pedidos.idempleados, pedidos.fecha_pedido, pedidos.fecha_ingreso, pedidos.cantidad_pedido, pedidos.precio_pedido, pedidos.estado_pedido, stock.idstock, stock.descripcion_art, empleados.idempleados, empleados.dni_empleado, empleados.nom_empleado, empleados.ape_empleado FROM pedidos INNER JOIN stock ON pedidos.id_stock = stock.idstock INNER JOIN empleados ON pedidos.idempleados = empleados.idempleados WHERE pedidos.id_ped LIKE '$buscar%' OR empleados.idempleados LIKE '$buscar%' OR empleados.dni_empleado LIKE '$buscar%' OR empleados.nom_empleado LIKE '$buscar%' OR empleados.ape_empleado LIKE '$buscar%' OR stock.descripcion_art LIKE '$buscar%' OR pedidos.estado_pedido LIKE '$buscar%'";
  }else{ 
    $sql = "SELECT pedidos.id_ped, pedidos.id_stock, pedidos.idempleados, pedidos.fecha_pedido, pedidos.fecha_ingreso, pedidos.cantidad_pedido, pedidos.precio_pedido, pedidos.estado_pedido, stock.idstock, stock.descripcion_art, empleados.idempleados, empleados.dni_empleado, empleados.nom_empleado, empleados.ape_empleado FROM pedidos INNER JOIN stock ON pedidos.id_stock = stock.idstock INNER JOIN empleados ON pedidos.idempleados = empleados.idempleados";
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
    <title>Pedidos - Consultas</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="./pedidos.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <?php
      require '../header.php';
    ?>

    <main>
      <h1>Pedidos - Consultas</h1>
      <div class='principal-actions'>
        <a class='btn' href="./pedidos_buscar_stock.php">Realizar Pedido</a>
        <form target='_blank' action='./pedidos_pdf.php' method ='POST' enctype='multipart/form-data'>
          <input class='btn ped-report' type="submit" name='report_pdf' value='Generar Reporte PDF'>
        </form>
        <a class='btn excel' href="./pedidos_excel.php">Generar Excel</a>
      </div>
    
      <div class='table-container'>
        <table>
          <caption class='pedido-search'>
            <form action="" method='POST'>
              <label class='pedido-search-label' for="pedido_search">Busque su pedido aqui por ID, ID Empleados, Nombre del Empleado, Estado del Pedido o Descripcion del Articulo</label>
              <input class="input" type="search" name="pedido_search" id="pedido_search" placeholder="Busque su pedido aqui...">
              <input class='btn btn-search' type="submit" name='search' value='Buscar'>
            </form>
          </caption>
          <colgroup>
            <col span='1' class="pedido_id_stock">
            <col span='1' class="pedido_idempleados">
            <col span='1' class="pedido_fecha_pedido">
            <col span='1' class="pedido_fecha_ingreso">
            <col span='1' class="pedido_cantidad_pedido">
            <col span='1' class="pedido_precio_pedido">
            <col span='1' class="pedido_estado_pedido">
            <col span='1' class="pedido_id_pedido">
            <col span='1' style='background-color: blue'>
          </colgroup>
          <thead class='thead'>
            <tr>
              <th>Id Pedido</th>
              <th>ID Stock</th>
              <th>ID Emp.</th>
              <th>DNI Emp.</th>
              <th>Nombre Emp.</th>
              <th>Apellido Emp.</th>
              <th>Desc. Art.</th>
              <th>Fecha Pedido</th>
              <th>Fecha Ingreso</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>

          <tbody>
            <?php if($resultado){
              while($row = $resultado ->fetch(PDO::FETCH_ASSOC)){?>
              <tr>
                <td><?php echo $row['id_ped'];?></td>
                <td><?php echo $row['id_stock'];?></td>
                <td><?php echo $row['idempleados']; ?></td>
                <td><?php echo $row['dni_empleado']; ?></td>
                <td><?php echo $row['nom_empleado']; ?></td>
                <td><?php echo $row['ape_empleado']; ?></td>
                <td><?php echo $row['descripcion_art']; ?></td>
                <td><?php echo $row['fecha_pedido']; ?></td>
                <td><?php echo $row['fecha_ingreso']; ?></td>
                <td><?php echo $row['cantidad_pedido']; ?></td>
                <td><?php echo $row['precio_pedido']; ?></td>
                <td><?php echo $row['estado_pedido']; ?></td>
                <td class='ped_item-actions'>
                  <?php if($row['estado_pedido'] == 1){ ?>
                    <a href="./pedidos_cerrar.php?id=<?php echo $row['id_ped'];?>" onclick="return confirmDelete('Estas a punto de cerrar el pedido: ID: <?php echo $row['id_ped']; ?> - <?php echo $row['descripcion_art']; ?> ')">
                     <img src='../static/images/borrar.png' alt='Cerrar Pedido' title='Cerrar Pedido' width='20' height='20'>
                    </a>
                  <?php } ?>
                </td>
              </tr>
              <?php }?>
            <?php }else{
                echo "<h3>No hay ningun pedido registrado.</h3>";
            }?>
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