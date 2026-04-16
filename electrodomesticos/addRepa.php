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
    if (($estado === 'Reparacion Cobrada' && $hoy > $fechaGarantia) ||
        ($estado === 'Presupuesto Rechazado')){
        $electroFiltrados[] = $e;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agregar Reparacion</title>
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
        <form id="volverForm" action="altaElectro.php" method="post" style="display:none;">
            <input type="hidden" name="n_id" value="<?= $idCli ?>">
        </form>
        <h2 class="titulo">Electrodomésticos del Cliente: <?=ucwords($e->getNomCli())?> <?=ucwords($e->getApeCliente())?></h2>
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
        <div class="table-container">
            <table  class="tabla">
                <tr>
                    <th class="tabla-head">ID Electro</th>
                    <th class="tabla-head">Tipo</th>
                    <th class="tabla-head">Marca</th>
                    <th class="tabla-head">Modelo</th>
                    <th class="tabla-head">N° Serie</th>
                    <th class="tabla-head">Agregar</th>
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
                <?php endforeach; ?>

            </table>
        </div>
        <!-- Formulario oculto -->
         
            <div id="formReparacion" class="formulario-contenedor" >
                <h3 >Agregar Reparación a Electro: <span style="color:blue; text-align:center" id="infoElectro"></span></h3>
                <br>
                <form action="guardarAddReparacion.php" method="post" >
                    <input type="hidden" name="idCli" value="<?= $idCli ?>">
                    <input type="hidden" name="idElectro" id="idElectro">
                    
                    <div class="form-group">
                        <label class="label" for="desc">Descripción del problema - Según el cliente:</label>
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
                    <div class="form-group">
                        <label class="label" for="coment_e">Si es necesario, describa por qué regresa el Electro:</label>
                        <textarea class="input" name="coment_e" id="coment_e"></textarea>
                    </div>
                    <br>
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
                        <textarea class="input" name="comentario" id="comentario"></textarea>
                    </div>

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
    </main>
    <?= include('../footer.php');?>
</body>
 