<?php
include("../header.php");
require '../conexionPDO.php';
?>
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
  $clientes = [];
  $no_results_message = '';
  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $tipo = $_POST['tipo'] ?? '';
    $identificacion = $_POST['identificacion'] ?? '';
    $identificacion = trim($identificacion);
    if ($tipo && $identificacion) {
      if ($tipo == 'dni') {
        $sql = "SELECT 
        r.id_reparacion,
        r.idelectrodomesticos,
        e.marca,
        e.modelo,
        c.nom_cliente,
        c.ape_cliente,
        r.fecha_inicio,
        r.fecha_finalizacion,
        e.descripcion
    FROM 
        reparaciones AS r
    LEFT JOIN 
        electrodomesticos AS e ON r.idelectrodomesticos = e.idelectrodomesticos
    INNER JOIN 
        clientes AS c ON e.idclientes = c.idclientes 
    WHERE 
        c.dni_cliente = :identificacion";
      } else {
        $sql = "SELECT 
        r.id_reparacion,
        r.idelectrodomesticos,
        e.marca,
        e.modelo,
        c.nom_cliente,
        c.ape_cliente,
        r.fecha_inicio,
        r.fecha_finalizacion,
        e.descripcion
    FROM 
        reparaciones AS r
    LEFT JOIN 
        electrodomesticos AS e ON r.idelectrodomesticos = e.idelectrodomesticos
    INNER JOIN 
        clientes AS c ON e.idclientes = c.idclientes 
    WHERE 
        c.idclientes = :identificacion";
      }

      $stmt = $base->prepare($sql);
      $stmt->execute(['identificacion' => $identificacion]);

      if ($stmt->rowCount() > 0) {
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
      } else {
        $no_results_message = 'No se encontraron resultados.';
      }
    }
  }

  try {
    $sql_clientes = "SELECT idclientes, CONCAT(nom_cliente, ' ', ape_cliente) AS nombre_completo FROM clientes";
    $stmt_clientes = $base->prepare($sql_clientes);
    $stmt_clientes->execute();
    $clientes_lista = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
  } catch (Exception $e) {
    $error = "Error al obtener clientes: " . $e->getMessage();
  }
  ?>

  <main>
    <div class="contenedor-consulta">
      <h1>Consultar electrodoméstico</h1>

      <div class="formulario-consulta">
        <form method="post">
        <div class="form-add">
          <h4>Buscar Cliente - Seleccione opción</h4>
          
            <table class="tabla-consulta">
              <tr>
                <td class="label">
                  <input type="radio" id="dni" name="tipo" value="dni" required />
                  <label for="dni">DNI</label>
                </td>
                <td class="label">
                  <input type="radio" id="id" name="tipo" value="id" required />
                  <label for="id">ID</label>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <div class="input-identificacion">
                    <input type="text" id="identificacion" name="identificacion" placeholder="Ingrese DNI/ID Cliente" required />
                  </div>
                </td>
              </tr>
            </table>

            <div class="botones">
              <button type="submit" class="btn-e">Buscar</button>
              <button type="button" onclick="window.location.href='electrodomesticos.php';" class="btn-e">Cancelar</button>
            </div>
        </div>
        </form>
      </div>
      <?php if (!empty($clientes)): ?>
        <h3>Resultados</h3>
        <div class="resultados">
          <?php foreach ($clientes as $cliente): ?>
            <div class="tarjeta" onclick="toggleDetalles(this)">
              <div class="campo">
                <strong>ID Reparación:</strong> <?php echo htmlspecialchars($cliente['id_reparacion']); ?>
              </div>
              <div class="campo">
                <strong>ID Electrodoméstico:</strong> <?php echo htmlspecialchars($cliente['idelectrodomesticos']); ?>
              </div>
              <div class="campo">
                <strong>Marca:</strong> <?php echo htmlspecialchars($cliente['marca']); ?>
              </div>
              <div class="campo">
                <strong>Modelo:</strong> <?php echo htmlspecialchars($cliente['modelo']); ?>
              </div>
              <div class="campo">
                <strong>Nombre del Cliente:</strong> <?php echo htmlspecialchars($cliente['nom_cliente']); ?>
              </div>
              <div class="campo">
                <strong>Apellido del Cliente:</strong> <?php echo htmlspecialchars($cliente['ape_cliente']); ?>
              </div>
              <div class="campo">
                <strong>Fecha de Inicio:</strong> <?php echo htmlspecialchars($cliente['fecha_inicio']); ?>
              </div>
              <div class="campo">
                <strong>Fecha de Finalización:</strong> <?php echo htmlspecialchars($cliente['fecha_finalizacion']); ?>
              </div>
              <div class="detalles">
                <p><strong>Mas Detalles</strong></p>
                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($cliente['descripcion']); ?></p> <!-- Agregado -->
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="botones">
          <button class="botones-electro" onclick="document.getElementById('identificacion').value=''; document.getElementsByName('tipo')[0].checked=false; document.getElementsByName('tipo')[1].checked=false; document.querySelector('.resultados').innerHTML='';">Nueva Consulta</button>
        </div>
      <?php elseif ($no_results_message): ?>
        <p style="color: red; text-align: center; font-weight: bold; font-size: 26px; margin-top: 20px;"><?php echo $no_results_message; ?></p>
      <?php endif; ?>
    </div>
    </div>
  </main>
  <script>
    function toggleDetalles(tarjeta) {
      const detalles = tarjeta.querySelector('.detalles');
      const todasLasTarjetas = document.querySelectorAll('.tarjeta');
      todasLasTarjetas.forEach(t => {
        if (t !== tarjeta) {
          t.style.display = 'none';
        }
      });
      if (detalles.style.display === "block") {
        detalles.style.display = "none";
        todasLasTarjetas.forEach(t => {
          t.style.display = 'block';
        });
      } else {
        detalles.style.display = "block";
      }
    }
  </script>
  <?php
    require '../footer.php';
  ?>

</body>

</html>