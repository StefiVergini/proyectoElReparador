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
      <div class='stock_search-prov-titles'>
        <h1>Stock - Agregar Articulo</h1>
        <h2>Busque entre los proveedores listados.</h2>
      </div>
        
      <div>
        <form class='form-search-prov'action="stock_agregar.php" method="post">
          <div>
            <label class='label' for="stock_agre-prov">CUIT/DNI Proveedor</label>
            <input class='input'type="number" id="stock_agre-prov" name="stock_agre-prov" required>
          </div>
          
          <div class='flex-container'>
            <input class='btn' type="submit" value="Aceptar" name='stock_saveProv'>
            <a class='btn cancel' href="./stock_Consultas.php">Cancelar</a>
          </div>
        </form>
      </div>    
    </main>
    <?php
      require '../footer.php';
    ?>
</body>
</html>