<?php
    header('Content-Type:application/xls');
    header('Content-Disposition: attachment; filename=stock.xls');

    require '../conexionPDO.php';

    try{
        $sql = "SELECT * FROM stock";
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
    <caption class='stock-search'>
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
        </tr>

        <?php }?>
    <?php }?>

    </tbody>
</table>