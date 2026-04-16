<?php
include("../conexionPDO.php");
include("../electrodomesticos/electro_class.php");
include("clientes_class.php");
include("../empleados/empleados_class.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: altaElectro.php");
    exit;
}

include("../header.php");

$electros = new Electro($base);
$empleados = new Empleados($base);

$idCli = $_POST['id'] ?? null;
$dniCli = $_POST['dniCli'] ?? null;
$emailCli = $_POST['emailCli'] ?? null;
$telCli = $_POST['telCli'] ?? null;


$electroXCli = $electros->leerReparaciones(null, $idCli); 

$ultimasPorElectro = [];

foreach ($electroXCli as $e) {
    $idElectro = (int)$e->getIdElectro();
    $idRepa    = (int)$e->getIdReparacion();

    if (!isset($ultimasPorElectro[$idElectro]) || $idRepa > (int)$ultimasPorElectro[$idElectro]->getIdReparacion()) {
        $ultimasPorElectro[$idElectro] = $e;
    }
}

// ahora filtrá solo las últimas
$electroFiltrados = [];

foreach ($ultimasPorElectro as $e) {
    $electroFiltrados[] = $e;

}
$nombreCli = '';
$apellidoCli = '';

if (!empty($electroFiltrados)) {
    $nombreCli = $electroFiltrados[0]->getNomCli();
    $apellidoCli = $electroFiltrados[0]->getApeCliente();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Electros de un Cliente</title>
  <link rel="stylesheet" href="../static/styles/style.css" />
  <link rel="stylesheet" href="../static/styles/tablas.css" />
  <link rel="stylesheet" href="../static/styles/formUsarConTabla.css" />
  <script src="../static/js/funciones_empleados.js"></script>
  <script src="../static/js/funciones_select_nav.js"></script>

</head>

<body>
  <main>
        <button style= "border: none; font-size: 16px; font-weight:bold; margin-top: 10px;cursor: pointer; background-color: #8497c5;" type="button"  onclick="document.getElementById('volverForm').submit();">
            < Volver
        </button>
        <form id="volverForm" action="inicioClientes.php" method="post" style="display:none;">
            <input type="hidden" name="n_id" value="<?= $idCli ?>">
        </form>
        <h2 class="titulo">Electrodomésticos del Cliente: <?=ucwords($nombreCli)?> <?=ucwords($apellidoCli)?></h2>
        <div class="formulario-contenedor">
            <div class="form-group" >
                <p class="label">ID: <?= $idCli; ?></p>
            </div>
            <div class="form-group">
                <p class="label">DNI: <?= $dniCli; ?></p>
            </div>
            <div class="form-group">
                <p class="label">Teléfono: <?= $telCli; ?></p>
            </div>
            <div class="form-group">
                <p class="label">Email: <?= $emailCli; ?></p>
            </div>
        </div>
        <?php 
        if(empty($electroFiltrados)){
        ?>
            <h3 style="text-align: center;">El cliente no posee electrodomésticos cargados</h3>
            <div class="button-group" style="align-items: center; justify-content:center; margin-top:20px;">
                <!-- Botón cancelar -->
                <button class="boton cancelar" type="button"  onclick="document.getElementById('volverForm').submit();">
                    Volver
                </button>
            </div>
            <!-- Formulario oculto para volver a altaElectro con IdCliente -->
            <form id="volverForm" action="inicioClientes.php" method="post" style="display:none;">
                
            </form>
        <?php 
        }else{
        ?>
        <div class="table-container">
            <table  class="tabla">
                <tr>
                    <th class="tabla-head">ID Electro</th>
                    <th class="tabla-head">Tipo</th>
                    <th class="tabla-head">Marca</th>
                    <th class="tabla-head">Modelo</th>
                    <th class="tabla-head">N° Serie</th>
                    <th class="tabla-head">Historial</th>
                </tr>
                
                <?php foreach ($electroFiltrados as $e): ?>
                    <tr onclick="cargarReparacion(<?=   $e->getIdElectro() ?>,'<?= ucwords($e->getNomTipo()) ?>', '<?= ucwords($e->getMarca()) ?>', '<?= strtoupper($e->getModelo()) ?>')">
                        <td class='tabla-data'><?= $e->getIdElectro() ?></td>
                        <td class='tabla-data'><?= ucwords($e->getNomTipo()) ?></td>
                        <td class='tabla-data'><?= ucwords($e->getMarca()) ?></td>
                        <td class='tabla-data'><?= strtoupper($e->getModelo()) ?></td>
                        <td class='tabla-data'><?= $e->getNumSerie() ?></td>
                        <td class='tabla-data'>
                            <a href="../electrodomesticos/historialDeUnElectro.php?idElectro=<?= $e->getIdElectro()  ?>" target="_blank">
                                <button type="button" class="btn-iconos"><img src='../static/images/history-button.png' alt='Historial del Electro' title='Historial del Electro' width='20' height='20'></button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php } ?>
            </table>
        </div>
    </main>
    <?= include('../footer.php');?>
</body>