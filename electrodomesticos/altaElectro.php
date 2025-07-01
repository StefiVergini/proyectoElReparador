<?php
// INCLUIMOS LAS DEPENDENCIAS Y ARCHIVOS NECESARIOS
include("../header.php");
include("../conexionPDO.php");
include("electro_class.php");
include("../clientes/clientes_class.php");
include("../empleados/empleados_class.php");

$clientes = new Clientes($base);
$electros = new Electro($base);
$empleados = new Empleados($base);

$cliente = null;                // Aca se guarda el cliente encontrado
$mostrarFormElectro = false;    // Flag que indica si se mostrar el formulario de alta del electro
$mensajeBusqueda = "";

// PRIMERO: PROCESAR PETICIONES DE REACTIVACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_reactivacion'])) {
  $idCli = $_POST['idCli']; // Tomamos el ID del cliente
  if ($_POST['confirmar_reactivacion'] == '1') {
    // Reactivamos al cliente
    $clientes->actualizarCliente($idCli);
    echo "<p>Cliente reactivado correctamente. Podés continuar con el alta del electrodoméstico.</p>";
    // Re-obtenemos el cliente para tener la versión actualizada
    $cliente = $clientes->obtenerUnCli($idCli);
    $mostrarFormElectro = true;
  } else {
    // En caso de que seleccione 'No', redirigimos a la pagina de inicioElectro
    header("Location: inicioElectro.php");
    exit();
  }
}

// SEGUNDO: PROCESAR LOS DATOS DE BÚSQUEDA DEL CLIENTE
if (
  $_SERVER['REQUEST_METHOD'] === 'POST'
  && !isset($_POST['confirmar_reactivacion'])
  && (
    (!empty($_POST['n_id']) && trim($_POST['n_id']) !== '')
    || (!empty($_POST['dni']) && trim($_POST['dni']) !== '')
  )
) {
  // Recibimos los datos enviados (ID o DNI)
  $id  = isset($_POST['n_id']) ? $_POST['n_id'] : null;
  $dni = isset($_POST['dni'])  ? $_POST['dni']  : null;

  // Buscamos el cliente según el dato recibido
  if ($dni) {
    $cliente = $clientes->obtenerUnDni($dni);
  } elseif ($id) {
    $cliente = $clientes->obtenerUnCli($id);
  }

  // Si se encuentra un cliente
  if (!empty($cliente) && is_object($cliente)) {
    if ($cliente->getEstadoCli() == 0) {
      // Se mostrará el formulario para la reactivación (lo renderizamos más adelante)
    } elseif ($cliente->getEstadoCli() == 1) {
      $mostrarFormElectro = true;
    }
  } else {
    // Solo asignamos el mensaje si se realizó una búsqueda con valores no vacíos
    $mensajeBusqueda = "<div class='form-group'><label class='label'>No se ha encontrado cliente con el ID o DNI indicado</label></div>";
  }
}
?>
<!-- A PARTIR DE ACÁ, VIENE LA PRESENTACIÓN HTML -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nueva Reparacion</title>
  <link rel="stylesheet" href="../static/styles/style.css" />
  <link rel="stylesheet" href="../static/styles/formularios.css" />
  <script src="../static/js/funciones_empleados.js"></script>
  <script src="../static/js/funciones_select_nav.js"></script>
</head>

<body>
  <main>
    <div class="formulario-contenedor">
      <h1>Nueva Reparacion</h1>
      <br>
      <hr>
      <br>
      <div style="align-items:center; width: 100%;">
        <form action="../clientes/altaCliente.php" method="post">
          <button class="btn" style="grid-column: 1 / -1; text-align:center; margin-left: 20%; margin-right:20%;">
            Cliente Nuevo
            <a href="altaElectro.php"></a>
          </button>

        </form>
      </div>
      <br>
      <hr>
      <h2>Buscar Cliente: </h2>
      <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="form-group">
          <label class="label" for="buscar_por">Seleccione una Opción de Búsqueda</label>
          <br>
          <div class="radio-group">
            <span class="label">ID</span>
            <input type="radio" name="buscar_por" id="buscar_por_id" value="n_id" onclick="mostrarCampoBusqueda()">
            <span class="label">DNI</span>
            <input type="radio" name="buscar_por" id="buscar_por_dni" value="dni" onclick="mostrarCampoBusqueda()">
          </div>
        </div>
        <!-- Campo de búsqueda por ID (oculto por defecto) -->
        <div id="campo_id" style="display:none;">
          <div class="form-group">
            <label class="label" for="n_id">Ingrese ID: </label>
            <input class="input" type="text" name="n_id"><br>
          </div>
        </div>
        <!-- Campo de búsqueda por DNI (oculto por defecto) -->
        <div id="campo_dni" style="display:none;">
          <div class="form-group">
            <label class="label" for="dni">Ingrese DNI: </label>
            <input class="input" type="text" name="dni"><br>
          </div>
        </div>
        <div class="button-group">
          <input class="boton submit" type="submit" value="Buscar">
        </div>
      </form>
      <?= $mensajeBusqueda; ?>
      <?php
      // A CONTINUACIÓN: MOSTRAR MENSAJE DE REACTIVACIÓN SI EL CLIENTE ESTÁ INACTIVO
      if (isset($cliente) && is_object($cliente) && $cliente->getEstadoCli() == 0) {
      ?>
        <div id="cliente_dado_baja" class="form-group">
          <p class="label" style="text-align:center;"><strong>Lo siento</strong>, el cliente
            <strong><?= $cliente->getNomCli() . ' ' . $cliente->getApeCli() ?></strong>
            con DNI <strong><?= $cliente->getDniCli() ?></strong> se encuentra dado de baja.
          </p>
          <p class="label" style="text-align:center;">¿Desea volver a darlo de alta para ingresar el electrodoméstico?</p>
          <br>
          <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
            <!-- Datos necesarios para la reactivación -->
            <input type="hidden" name="dni" value="<?= $cliente->getDniCli() ?>">
            <input type="hidden" name="idCli" value="<?= $cliente->getIdCli() ?>">
            <input type="hidden" name="confirmar_reactivacion" value="1">
            <input class="boton submit" type="submit" value="Si">
            <button class="boton cancelar" type="button" onclick="window.location.href='inicioElectro.php'">No</button>
          </form>
        </div>
      <?php
      }

      // MOSTRAR EL FORMULARIO DE ALTA DEL ELECTRODOMÉSTICO SI CORRESPONDE
      if ($mostrarFormElectro && isset($cliente) && is_object($cliente)) {
      ?>
        <form action="guardarAltaElectro.php" method="post">
          <div class="form-group">
            <p class="label">ID: <?= $cliente->getIdCli(); ?></p>
          </div>
          <div class="form-group">
            <p class="label">DNI: <?= $cliente->getDniCli(); ?></p>
          </div>
          <div class="form-group">
            <p class="label">Nombre y Apellido: <?= $cliente->getNomCli() . ' ' . $cliente->getApeCli(); ?></p>
          </div>
          <div class="form-group">
            <p class="label">Teléfono: <?= $cliente->getTelCli(); ?></p>
          </div>
          <div class="form-group">
            <p class="label">Dirección: <?= $cliente->getDirCli(); ?></p>
          </div>
          <div class="form-group">
            <p class="label">Email: <?= $cliente->getEmailCli(); ?></p>
          </div>


          <h2>Detalles Electrodoméstico:</h2>
          <br><br>
          <input type="hidden" name="n_id" value="<?= $cliente->getIdCli(); ?>">
          <div class="form-group">
            <label class="label" for="tipo">Tipo de Electrodoméstico</label>
            <select name="tipo" id="tipo">
              <?php
              $tipos = $electros->leerTipoElectro();
              foreach ($tipos as $tipo) {
                $tipoId = $tipo->getTipoElectro();
                $nomTipo = $tipo->getNomTipo();
                echo '<option value="' . $tipoId . '">' . ucwords($nomTipo) . '</option>';
              }
              ?>
              <option value="nuevo_tipo">-- Agregar nuevo --</option>
            </select>
          </div>
          <div class="form-group">
            <label class="label" for="marca">Marca</label>
            <input class="input" type="text" name="marca" id="marca" required>
          </div>
          <div class="form-group">
            <label class="label" for="modelo">Modelo</label>
            <input class="input" type="text" name="modelo" id="modelo" required>
          </div>
          <div class="form-group">
            <label class="label" for="num_serie">Número de Serie</label>
            <input class="input" type="text" name="num_serie" id="num_serie">
          </div>
          <div class="form-group">
            <label class="label" for="desc">Descripción del problema - Según el cliente:</label>
            <textarea class="input" name="desc" id="desc" cols="40" rows="6" required></textarea>
          </div>
          <!-- Selección de técnicos -->
          <div class="form-group">
            <label class="label" for="tecnicos">Seleccione Técnico para la Reparación</label>
            <select name="tecnicos" id="tecnicos">
              <?php
              $empleado = $empleados->tecnicosXLocal($local);
              foreach ($empleado as $tecnico) {
                $tec = $tecnico->getIdEmp();
                $nombreTecnico = $tecnico->getNomEmp() . " " . $tecnico->getApeEmp();
                echo '<option value="' . $tec . '">' . ucwords($nombreTecnico) . '</option>';
              }
              ?>
            </select>
          </div>
          <h2>Detalle del cobro - Arancel fijo:</h2>
          <br><br>
          <div class="form-group">
            <label class="label" for="medio_pago">Medio de Pago</label>
            <input type="radio" name="medio_pago" value="efectivo" required onclick="mostrarComprobante(this.value)">
            <label for="efectivo">Efectivo</label><br>
            <input type="radio" name="medio_pago" value="transferencia" required onclick="mostrarComprobante(this.value)">
            <label for="transferencia">Transferencia Bancaria</label><br>
          </div>
          <div class="form-group" id="comprobante-container" style="display: none;">
            <label class="label" for="nro_comprobante">N° de Comprobante</label>
            <input type="text" name="nro_comprobante" id="nro_comprobante">
          </div>
          <div class="form-group">
            <label class="label" for="monto_fijo">Monto Fijo Abonado</label>
            <input class="input" type="number" name="monto_fijo" id="monto_fijo" placeholder="$" required>
          </div>
          <div class="form-group">
            <label class="label" for="comentario">Puede agregar algún comentario sobre el cobro:</label>
            <textarea class="input" name="comentario" id="comentario" cols="40" rows="6"></textarea>
          </div>
          <div class="button-group">
            <input class="boton submit" type="submit" value="Agregar">
            <button class="boton cancelar" type="button" onclick="window.location.href='inicioElectro.php'">Cancelar</button>
          </div>
        </form>
      <?php } ?>
    </div>
  </main>
  <script>
    function mostrarComprobante(valor) {
      const campo = document.getElementById('comprobante-container');
      const input = document.getElementById('nro_comprobante');
      if (valor === 'transferencia') {
        campo.style.display = 'block';
        input.required = true;
        input.value = ''; // Limpiar valor si hubiera algo
      } else {
        campo.style.display = 'none';
        input.required = false;
        input.value = '-'; // Asignar valor por defecto
      }
    }
  </script>
  <script>
    document.getElementById('tipo').addEventListener('change', function() {
      if (this.value === 'nuevo_tipo') {
        window.location.href = 'altatipoelectro.php';
      }
    });
  </script>
</body>

</html>