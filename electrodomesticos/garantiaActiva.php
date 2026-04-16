<?php
include("../conexionPDO.php");
include("electro_class.php");
include("../clientes/clientes_class.php");
include("../empleados/empleados_class.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: altaElectro.php");
    exit;
}

include("../header.php");

$electros = new Electro($base);
$empleados = new Empleados($base);

$idCli = $_POST['idCli'] ?? null;
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
$hoy = date('Y-m-d');
foreach ($ultimasPorElectro as $e) {
    $estado = trim($e->getEstadoPresu());
    $fechaGarantia = $e->getFechaFinGarantia();
    if ($estado === 'Reparacion Cobrada' && $fechaGarantia && $hoy <= $fechaGarantia) {
        $electroFiltrados[] = $e;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Garantía de una Reparación</title>
  <link rel="stylesheet" href="../static/styles/style.css" />
  <link rel="stylesheet" href="../static/styles/tablas.css" />
  <link rel="stylesheet" href="../static/styles/formUsarConTabla.css" />
  <script src="../static/js/funciones_empleados.js"></script>
  <script src="../static/js/funciones_select_nav.js"></script>
  <style>
   
  </style>
</head>

<body>
  <main>
        <button style= "border: none; font-size: 16px; font-weight:bold; margin-top: 10px;cursor: pointer; background-color: #8497c5" type="button"  onclick="document.getElementById('volverForm').submit();">
            < Volver
        </button>
        <form id="volverForm" action="altaElectro.php" method="post" style="display:none;">
            <input type="hidden" name="n_id" value="<?= $idCli ?>">
        </form>
        <h2 class="titulo">Electrodomésticos con Garantía Activa </h2>
        <h3>Cliente: <?=ucwords($e->getNomCli())?> <?=ucwords($e->getApeCliente())?></h3>
        <br>
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
            <h3 style="text-align: center;">El cliente no posee electrodomésticos con garantía activa</h3>
            <div class="button-group" style="align-items: center; justify-content:center; margin-top:20px;">
                <!-- Botón cancelar -->
                <button class="boton cancelar" type="button"  onclick="document.getElementById('volverForm').submit();">
                    Volver
                </button>
            </div>
            <!-- Formulario oculto para volver a altaElectro con IdCliente -->
            <form id="volverForm" action="altaElectro.php" method="post" style="display:none;">
                <input type="hidden" name="n_id" value="<?= $idCli ?>">
            </form>
        <?php 
        }else{
        ?>
        <div class="table-container">
            <table  class="tabla">
                <tr>
                    <th class="tabla-head">ID Electro</th>
                    <th class="tabla-head">ID Reparacion</th>
                    <th class="tabla-head">Técnico</th>
                    <th class="tabla-head">Tipo</th>
                    <th class="tabla-head">Marca</th>
                    <th class="tabla-head">Modelo</th>
                    <th class="tabla-head">N° Serie</th>
                    <th class="tabla-head">Detalle Repa</th>
                    <th class="tabla-head">Agregar</th>
                    <th class="tabla-head">Historial</th>
                </tr>
                
                <?php 
                $electroUnicos = [];
                foreach ($electroFiltrados as $e){
                    $idElectro = $e->getIdElectro();
                    $fechaRetiro = $e->getFechaDeRetiro(); 

                    if (!isset($electroUnicos[$idElectro])) {
                        // si no existe todavía, lo agregamos el electrodomestico
                        $electroUnicos[$idElectro] = $e;
                    } else {
                        // si ya existe, comparamos la fecha y nos quedamos con el más nuevo
                        $fechaExistente = $electroUnicos[$idElectro]->getFechaDeRetiro();
                        if ($fechaRetiro > $fechaExistente) {
                            $electroUnicos[$idElectro] = $e;
                        }
                    }
                }
                
                foreach ($electroUnicos as $e){
                    $idRepaAnt = $e->getIdReparacion();?>
                    <tr onclick="cargarReparacion(<?=   $e->getIdElectro() ?>,'<?= ucwords($e->getNomTipo()) ?>', '<?= ucwords($e->getMarca()) ?>', '<?= strtoupper($e->getModelo()) ?>')">
                        <td class='tabla-data'><?= $e->getIdElectro() ?></td>
                        <td class='tabla-data'><?= $idRepaAnt?></td>
                        <td class='tabla-data'><?= ucwords($e->getNomTecnico())?> <?=  ucwords($e->getApeTecnico())  ?></td>
                        <td class='tabla-data'><?= ucwords($e->getNomTipo()) ?></td>
                        <td class='tabla-data'><?= ucwords($e->getMarca()) ?></td>
                        <td class='tabla-data'><?= strtoupper($e->getModelo()) ?></td>
                        <td class='tabla-data'><?= $e->getNumSerie() ?></td>
                        <td class='tabla-data'><?= strtoupper($e->getObservaciones()) ?></td>
                        <td class='tabla-data'>
                            <button type="button" class="btn-iconos" onclick="event.stopPropagation(); cargarReparacion(<?=  $e->getIdElectro() ?>, '<?= $e->getMarca() ?>', '<?= $e->getModelo() ?>')">
                                <img src='../static/images/add-button.png' alt='Nueva Reparacion' title='Nueva Reparacion' width='20' height='20'>
                            </button>
                        </td>
                        <td class='tabla-data'>
                            <a href="historialDeUnElectro.php?idElectro=<?= $e->getIdElectro()  ?>" target="_blank">
                                <button type="button" class="btn-iconos"><img src='../static/images/history-button.png' alt='Historial del Electro' title='Historial del Electro' width='20' height='20'></button>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
        <!-- Formulario oculto -->
         
            <div id="formReparacion" class="formulario-contenedor" >
                <h3 >Garantía de Reparación a Electro: <span style="color:blue; text-align:center" id="infoElectro"></span></h3>
                <br>
                <form action="guardarGarantiaAct.php" method="post" >
                    <input type="hidden" name="idCli" value="<?= $idCli ?>">
                    <input type="hidden" name="idElectro" id="idElectro">
                    <input type="hidden" name="idRepaAnt" value="<?= $idRepaAnt ?>">
                    
                    
                    <div class="form-group">
                        <label class="label" for="desc">Descripción del problema que regresa por la garantía - Según el cliente:</label>
                        <textarea class="input" name="desc" id="desc" required></textarea>
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
                    <br>
                    <h2 style="text-align: center;">Se evaluará si el mal funcionamiento del electro es por la reparación anterior que se realizó o el motivo es otro.</h2>

                    <br>
                    <div class="button-group">
                        <button type="submit" class="boton submit">Guardar</button>
                        <!-- Botón cancelar -->
                        <button class="boton cancelar" type="button"  onclick="document.getElementById('cancelarForm').submit();">
                            Cancelar
                        </button>
                    </div>
                </form>
                <!-- Formulario oculto para volver a altaElectro con IdCliente -->
                <form id="cancelarForm" action="altaElectro.php" method="post" style="display:none;">
                    <input type="hidden" name="n_id" value="<?= $idCli ?>">
                </form>

            </div>


    <script>
    function cargarReparacion(idElectro,nomTipo, marca, modelo) {
        const form = document.getElementById('formReparacion');
        const infoElectro = document.getElementById('infoElectro');
        const inputElectro = document.getElementById('idElectro');

        // si ya está visible y se vuelve a apretar, lo ocultamos
        if (form.style.display === 'block' && inputElectro.value == idElectro) {
            form.style.display = 'none';
            inputElectro.value = '';
            infoElectro.innerText = '';
        } else {
            form.style.display = 'block';
            form.style.backgroundColor = '#1515c043';
            inputElectro.value = idElectro;
            infoElectro.innerText = nomTipo + " " + marca + " " + modelo;
        }
    }
    
    </script>
    </main>
    <?= include('../footer.php');?>
</body>
 