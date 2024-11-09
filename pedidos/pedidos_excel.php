<?php
    header('Content-Type:application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename=pedidos.xls');

    require '../conexionPDO.php';

    try{
        $sql = "SELECT pedidos.id_ped, pedidos.id_stock, pedidos.idempleados, pedidos.fecha_pedido, pedidos.fecha_ingreso, pedidos.cantidad_pedido, pedidos.precio_pedido, pedidos.estado_pedido, stock.idstock, stock.descripcion_art, empleados.idempleados, empleados.dni_empleado, empleados.nom_empleado, empleados.ape_empleado FROM pedidos INNER JOIN stock ON pedidos.id_stock = stock.idstock INNER JOIN empleados ON pedidos.idempleados = empleados.idempleados";

        $resultado=$base->prepare($sql);
        $resultado -> execute();
    
      }catch(Exception $e){
        echo $e-> getMessage();
        echo "Línea del error: " . $e->getLine();
      }finally{
          $base=null;
      }  
?>
<meta charset="UTF-8">
<table>
          
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
              <th>Id Pedido</th>
            </tr>
          </thead>

          <tbody>
            <?php if($resultado){
              while($row = $resultado ->fetch(PDO::FETCH_ASSOC)){?>
              <tr>
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
                <td><?php echo $row['id_ped'];?></td>
              </tr>
              <?php }?>
            <?php }else{
                echo "<h3>No hay ningun pedido registrado.</h3>";
            }?>
          </tbody>
        </table>