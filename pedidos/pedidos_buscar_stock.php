<?php
    /*Esta pagina viene desde pedidos_consulta.php */
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
    ?>

    <main>
        <h1>Pedido - Buscar Articulo</h1>
        <div class='table-container'>
            <table>
            <colgroup>
                <col class="stock_id-art">
                <col class="stock_desc">
                <col class="stock_cant">
                <col class="stock_id-prov">
                <col class="stock_tipo">
                <col class="stock_actions">
            </colgroup>
            <thead>
                <tr>
                <th>ID Articulo</th>
                <th>Descripcion</th>
                <th>Cantidad</th>
                <th>ID Proveedor</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Realizar Pedido</th>
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
                    <td><?php echo  $row['tipo_stock'] ?></td>
                    <td><?php echo  $row['estado_stock']==1?'Activo':'Inactivo'; ?></td>
                    <td>
                        <?php if($row['estado_stock'] === 1){ ?>
                            <a href="./pedidos_agregar.php?id=<?php echo $row['idstock'];?>">Realizar pedido</a>
                        <?php }else{ ?>
                            <a href="../stock/stock_Consultas.php">Stock Consultas</a>
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
</body>
</html>