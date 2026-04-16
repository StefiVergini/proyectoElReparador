<?php
include("../conexionPDO.php");
include("electro_class.php");
include("../header.php");

$idElectro = $_GET['idElectro'] ?? null;

$reparacionesObj = new Electro($base);
$reparaciones = [];

if ($idElectro) {
    $reparaciones = $reparacionesObj->leerReparaciones($idElectro, null);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de un Electro</title>
  <link rel="stylesheet" href="../static/styles/style.css" />
  <link rel="stylesheet" href="../static/styles/tablas.css" />
  <link rel="stylesheet" href="../static/styles/formUsarConTabla.css" />
  <style>

    h2 {
      color: #0201B4;
      text-align: center;
    }
   /* Contenedor de la tabla */
    .table-container {
      width: 95%;
      max-width: 98%; /* controla el ancho máximo */
      margin: 20px auto;
      background: white;
      padding: 5px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      overflow-x: auto; /* solo aparece scroll si hace falta */
    }

    /* Tabla */
    .tabla {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      text-align: center;
    }

    /* Celdas */
    th, td {
      padding: 7px 12px;
      border: 1px solid #ccc;
      white-space: normal; /* permite que el texto se ajuste */
      word-wrap: break-word;
    }

    /* Encabezado */
    th {
      background: #0201B4;
      color: #fff;
    }

    /* Alternancia de color */
    tr:nth-child(even) {
      background: #f9f9f9;
    }

    /* Efecto hover */
    tr:hover {
      background-color: #eef3ff;
      transition: background-color 0.2s ease-in-out;
    }
    .volver {
      display: block;
      margin: 20px auto;
      padding: 10px 20px;
      border: none;
      background: #0201B4;
      color: white;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      text-align: center;
      width: fit-content;
    }
    .volver:hover {
      background: #010088;
    }
    .info-electro {
      background: white;
      padding: 15px;
      margin-top: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    .info-electro p {
      margin: 6px 0;
      font-size: 18px;
    }
    .info-electro p strong{
      color: #010088;
    }
  </style>
  <script src="../static/js/funciones_empleados.js"></script>
  <script src="../static/js/funciones_select_nav.js"></script>

</head>
<body>

<?php if ($idElectro && !empty($reparaciones)): 
      $primera = $reparaciones[0]; // todos comparten info del mismo electro
      
?>
  <h2 class="titulo">Historial del Electrodoméstico</h2>

  <div class="formulario-contenedor info-electro">
    <p class="label"><strong>Cliente:</strong> <?= htmlspecialchars(ucwords($primera->getNomCli()) . " " . ucwords($primera->getApeCliente())) ?></p>
    <p class="label"><strong>Email:</strong> <?= htmlspecialchars($primera->getEmailCliente()) ?></p>
    <p class="label"><strong>ID:</strong> <?= htmlspecialchars($primera->getIdElectro()) ?></p>
    <p class="label"><strong>Tipo:</strong> <?= htmlspecialchars(strtoupper($primera->getNomTipo())) ?></p>
    <p class="label"><strong>Marca:</strong> <?= htmlspecialchars(strtoupper($primera->getMarca())) ?></p>
    <p class="label"><strong>Modelo:</strong> <?= htmlspecialchars(strtoupper($primera->getModelo())) ?></p>
    <?php if ($primera->getNumSerie()): ?>
      <p class="label"><strong>N° de Serie:</strong> <?= htmlspecialchars($primera->getNumSerie()) ?></p>
    <?php endif; ?>
    <br>
    
  </div>
  <div class="table-container">
    <table class="tabla">
      <thead>
        <tr>
          <th class="tabla-head">ID Reparación</th>
          <th class="tabla-head">Fecha Ingreso</th>
          <th class="tabla-head">Fecha Inicio Repa</th>
          <th class="tabla-head">Fecha Fin Est</th>
          <th class="tabla-head">Fecha Fin</th>
          <th class="tabla-head">Fecha Fin Garantia</th>
          <th class="tabla-head">Técnico</th>
          <th class="tabla-head">Presupuestador</th>
          <th class="tabla-head">Monto Inicial $</th>
          <th class="tabla-head">Monto Final $</th>
          <th class="tabla-head">Estado</th>
          <th class="tabla-head">Detalle Repa</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reparaciones as $r){ 
           $fecha_ing      = $r->getFechaIngElectro()   ? date("d/m/Y", strtotime($r->getFechaIngElectro()))   : '-';
          $fecha_inicio   = $r->getFechaInicio()        ? date("d/m/Y", strtotime($r->getFechaInicio()))       : '-';
          $fecha_est_repa = $r->getFechaFinEst()        ? date("d/m/Y", strtotime($r->getFechaFinEst()))       : '-';
          $fecha_fin      = $r->getFechaFin()           ? date("d/m/Y", strtotime($r->getFechaFin()))          : '-';
          $fecha_finGa    = $r->getFechaFinGarantia()   ? date("d/m/Y", strtotime($r->getFechaFinGarantia()))  : '-';
      ?>
        <tr>
          <td class='tabla-data'><?= htmlspecialchars($r->getIdReparacion()) ?></td>
          <td class='tabla-data'><?= htmlspecialchars($fecha_ing) ?></td>
          <td class='tabla-data'><?= htmlspecialchars($fecha_inicio) ?></td>
          <td class='tabla-data'><?= htmlspecialchars($fecha_est_repa) ?></td>
          <td class='tabla-data'><?= htmlspecialchars($fecha_fin) ?></td>
          <td class='tabla-data'><?= htmlspecialchars($fecha_finGa) ?></td>
            <td class='tabla-data'><?= htmlspecialchars($r->getNomTecnico() . " " . $r->getApeTecnico()) ?></td>
            <td class='tabla-data'><?= htmlspecialchars($r->getNomEmpPresu() . " " . $r->getApeEmpPresu()) ?></td>
            <td class='tabla-data'><?= htmlspecialchars($r->getMontoFijoIni() ?: '-') ?></td>
            <td class='tabla-data'><?= htmlspecialchars($r->getMontoFinRepa() ?: '-') ?></td>
            <td class='tabla-data'><?= htmlspecialchars($r->getEstadoPresu() ?: '-') ?></td>
            <td class='tabla-data'><?= htmlspecialchars($r->getObservaciones() ?: '-') ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
<?php elseif ($idElectro): ?>
  <p style="text-align:center; color:red;">Este electrodoméstico no tiene reparaciones registradas.</p>
<?php else: ?>
  <p style="text-align:center; color:red;">No se seleccionó ningún electrodoméstico.</p>
<?php endif; ?>

<a href="javascript:window.close();" class="volver">Cerrar pestaña</a>
    

  <?= include('../footer.php');?>

</body>
</html>
