<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Electrodomesticos</title>
  <link rel="stylesheet" href="../static/styles/style.css" />
  <link rel="stylesheet" href="../static/styles/electro/electro.css" />


  <script src="../static/js/funciones_select_nav.js"></script>
</head>

<body>

  <?php
  include "../header.php";
  ?>

  <main>
    <h3>Electrodomésticos</h3>
    <div class="contenedor-electro">


      <div class="pantalla-electro">
        <button type="button" class="botones-electro"
          onclick="window.location.href='consulta.php';">
          Consultar Reparacion
        </button>

        <button type="button" class="botones-electro"
          onclick="window.location.href='agregar_reparacion.php';">
          Agregar Orden de Reparación
        </button>
      </div>

    </div>
  </main>
  <?php
    require '../footer.php';
  ?>

</body>

</html>