<?php
include_once("../header.php");
include_once("../conexionPDO.php");
include_once("electro_class.php");

$electro = new Electro($base);

$rol_tecnico = $_SESSION['rol'];

// Inicializar filtros
$desde = '';
$hasta = '';
$clienteFiltro = '';
$busquedaRealizada = false;

// Por defecto, mostramos todas las reparaciones
$reparaciones = $electro->leerReparaciones();
$filtro_repas =[];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desde         = $_POST['desde'] ?? '';
    $hasta         = $_POST['hasta'] ?? '';
    $clienteFiltro = $_POST['dni']   ?? '';

    if ((!empty($desde) && !empty($hasta)) || !empty($clienteFiltro)){
        $reparaciones = $electro->filtrarHistorialReparaciones($desde, $hasta, $clienteFiltro);
        $busquedaRealizada = true;
    }else{
        $filtro_repas = $electro->leerReparaciones();
        if ((!empty($desde) && empty($hasta)) || (empty($desde) && !empty($hasta))) {
                $error = "Si deseas filtrar por fechas, debes ingresar ambas.";
                ?> <h2 style="grid-column: 1 / -1; text-align:center; color:red; background-color:white;"><?php echo $error;?> </h2><?php
            }
    }


}else{
    $reparaciones = $electro->leerReparaciones();
    $busquedaRealizada = false;
 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Electrodomesticos</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/card.css" />
    <link rel="stylesheet" href="../static/styles/filtrosPosicion.css" />
    <script src="../static/js/funciones_empleados.js"></script>
    <script src="../static/js/funciones_select_nav.js"></script>

    <script>
        function mostrarFiltros(filtroId) {
            event.preventDefault();
            var filtros = document.getElementById(filtroId);

            filtros.classList.toggle('mostrar');
            var gridContainer = document.querySelector('.grid-container');
            // Puedes agregar aquí algún ajuste al container de la cuadrícula si es necesario
            if (filtros.style.display === 'none' || filtros.style.display === '') {
                filtros.style.display = 'block';
                gridContainer.style.marginTop = '50px';  

            } else {
                filtros.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <h1 class="titulo">Historial de Reparaciones Cobradas</h1>
    <hr>
    <!-- Reparaciones Cobradas -->
    <div id="repaCobradas" class="grid-container" >
        <div class="div-con-botones" style="grid-column: 1 / -1; text-align:center;">
            <form action="inicioElectro.php" method="post">
                <button class='btn'> Electrodomesticos
                    <a href="inicioElectro.php"></a>
                </button>
            </form>
            <button class='btn' id="btn-filtro" type="button" onclick="mostrarFiltros('filtrosRepa')">
                Filtros
                <img src='../static/images/filter.png' alt='filtro' title='Filtrar' width='23' height='23'>
            </button>
        </div>

        <div class="div-con-botones" id="filtrosRepa">
            <form action="" method="post">
                <div class="form-group">
                    <h2 style="margin:5px;">Filtros elija por fecha o por cliente o ambos: </h2>
                    <h4>Se calcula entre fechas de ingreso de los electrodomésticos</h4>
                    <div id="desde" style="display: inline-block;">
                        <label class="label" for="desde">Fecha Desde: </label>
                        <input class="input" type="date" name="desde"><br>
                    </div>
                    <div id="hasta" style="display: inline-block;">
                        <label class="label" for="hasta">Fecha Hasta: </label>
                        <input class="input" type="date" name="hasta"><br>
                    </div>
                    <div id="filtro_cliente">
                        <label class="label" for="dni">Ingrese parte del Nombre o Apellido del Cliente: </label>
                        <input class="input" type="text" name="dni">
                        <button class="btn-iconos" type="submit">
                            <img src="../static/images/lupa.png" alt="Buscar" width="30" height="20" />
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        $contador_reparaciones = 0;
        foreach ($reparaciones as $rep):
            if ($rep->getEstadoPresu() == 'Reparacion Cobrada' ):  // En reparación
                $contador_reparaciones++;
                $fecha_inicio = date("d/m/Y", strtotime($rep->getFechaInicio()));
                $fecha_fin = date("d/m/Y", strtotime($rep->getFechaFin()));
                $fecha_retiro_electro = date("d/m/Y", strtotime($rep->getFechaDeRetiro()));
                $fecha_fin_garantia = date("d/m/Y", strtotime($rep->getFechaFinGarantia()));
                
                $descPresu = $rep->getObservaciones();
                $partes = explode(" Detalle de la Reparación: ", $descPresu);

                // La primera parte contiene "Materiales: ..."
                $materiales = isset($partes[0])
                    ? trim(str_replace("Materiales: ", "", $partes[0]))
                    : 'Sin materiales registrados';

                // La segunda parte contiene el detalle de la reparación
                $detalleReparacion = isset($partes[1])
                    ? trim($partes[1])
                    : 'Sin detalle de reparación';

                $presup = $rep->getPresupuesto();
                $monto_fijo = $rep->getMontoFijoIni();
                $cant_a_abonar = $presup - $monto_fijo;
                $medio_pago_fin = $rep->getMedioPagoFin();
        ?>
        
            <div class="card" onclick="expandirDetalle(<?= $rep->getIdReparacion() ?>)">

                <div class="card-header">Reparación Cobrada #<?= $rep->getIdReparacion() ?></div>
                <ul class="list-group">
                    <li class="list-group-item centered"><strong style="color:red;">Garantía Válida hasta:</strong> <?= $fecha_fin_garantia ?></li>
                    <li class="list-group-item centered"><strong>Fecha Retiro Electro:</strong> <?= $fecha_retiro_electro ?> </li>
                    <li class="list-group-item"><strong>Monto Abonado:</strong> <strong style="color:red;">$<?= $rep->getMontoFinRepa() ?> </strong> <strong>Medio de pago: </strong> <?= ucwords($medio_pago_fin) ?> </li>
                     <li class="list-group-item"><strong>Electrodoméstico:</strong> <?= ucwords($rep->getNomTipo()) . " - " . strtoupper($rep->getMarca()) ?><br>Modelo: <?= $rep->getModelo() ?></li>
                     <li class="list-group-item"><strong>Cliente:</strong> <?= ucwords($rep->getNomCli() . " " . $rep->getApeCliente()) ?></li>
                    <li class="list-group-item"><strong>Técnico Asignado:</strong> <?= ucwords($rep->getNomTecnico() . " " . $rep->getApeTecnico()) ?></li>
                    <li class="list-group-item" style="text-align: center;"><strong>Más Detalles</strong></li>
                    <li class="list-group-item" style="text-align: center;"><strong>&#9207;</strong></li>
                    
                </ul>
            </div>
        
        <?php endif; endforeach; ?>

        <?php if ($contador_reparaciones == 0): ?>
            <div style="grid-column: 1 / -1; text-align: center;">
                <h3>No hay Historial de Reparaciones Cobradas</h3>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Overlay para la tarjeta expandida -->
    <div class="overlay" id="overlay">
        <div class="expanded-card" id="detalle-expandido">
            <button class="close-btn" onclick="cerrarDetalle()">X</button>
            <div id="contenido-reparacion"></div>
        </div>
    </div>
    <?php
     if($busquedaRealizada){
            echo "<br><button class='btn' style='display:block; margin-left: auto; margin-right:auto; margin-bottom: 15px;'><a style='text-decoration:none; color:white;' href='historialReparacionesCobradas.php'>Volver</a></button>";
        }
    ?>
            
    <script>
        //Expandir tarjetas
            const reparaciones = <?= json_encode(array_map(function ($r) {
                return [
                    'id' => $r->getIdReparacion(),
                    'fechaIng' => $r->getFechaIngElectro() ? date("d/m/Y", strtotime($r->getFechaIngElectro())) : '-',
                    'marca' => strtoupper($r->getMarca() ?? ''),
                    'modelo' => $r->getModelo() ?? '',
                    'tipo' => ucwords($r->getNomTipo() ?? ''),
                    'numSerie' => $r->getNumSerie() ?? '',
                    'cliente' => ucwords(($r->getNomCli() ?? '') . " " . ($r->getApeCliente() ?? '')),
                    'email' => $r->getEmailCliente() ?? '',
                    'descripcion' => ucwords($r->getDescripcion() ?? ''),
                    'tecnico' => ucwords(($r->getNomTecnico() ?? '') . " " . ($r->getApeTecnico() ?? '')),
                    'estadoPresu' => $r->getEstadoPresu() ?? '',
                    'observaciones' => $r->getObservaciones() ?? '',
                    'fechaEnv' => $r->getFechaEnvioPresup() ? date("d/m/Y", strtotime($r->getFechaEnvioPresup())) : '-',
                    'fechaConfirm' => $r->getFechaConfirmReparacion() ? date("d/m/Y", strtotime($r->getFechaConfirmReparacion())) : '-',
                    'fechaGaran' => $r->getFechaFinGarantia() ? date("d/m/Y", strtotime($r->getFechaFinGarantia())) : '-',
                    'fechaCobro' => $r->getFechaCobroFin() ? date("d/m/Y", strtotime($r->getFechaCobroFin())) : '-',
                    'fechaFinEst' => $r->getFechaFinEst() ? date("d/m/Y", strtotime($r->getFechaFinEst())) : '-',
                    'fechaFin' => $r->getFechaFin() ? date("d/m/Y", strtotime($r->getFechaFin())) : '-',
                    'presupuesto' => $r->getPresupuesto() ?? '',
                    'montoFijo' => $r->getMontoFijoIni() ?? '',
                    'medioPago' => ucwords($r->getMedioPagoIni() ?? ''),
                    'nroCompro' => $r->getNroComproIni() ?? '',
                    'montoFijoFin' => $r->getMontoFinRepa() ?? '',
                    'medioPagoFin' => ucwords($r->getMedioPagoFin() ?? ''),
                    'nroComproFin' => $r->getNroComproFin() ?: '-',
                ];
            }, $reparaciones), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

        function expandirDetalle(id) {
           
            const reparacion = reparaciones.find(r => r.id == id);
            if (!reparacion) return;

            let materiales = "", detalleReparacion = "";
            if (reparacion.observaciones.includes("Detalle de la Reparación:")) {
                const partes = reparacion.observaciones.split(" Detalle de la Reparación: ");
                materiales = partes[0].replace("Materiales: ", "").trim();
                detalleReparacion = partes[1]?.trim() || "";
            }

            const contenido = document.getElementById("contenido-reparacion");

                contenido.innerHTML = `
                    <div class="card-header">
                        Reparación #${reparacion.id}
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item"><strong style='color:red'>Garantía Válida Hasta:</strong> ${reparacion.fechaGaran}</li>
                        <li class="list-group-item"><strong>Electrodoméstico:</strong> ${reparacion.marca} ${reparacion.modelo}<br>
                        Tipo: ${reparacion.tipo}<br>N° de Serie: ${reparacion.numSerie}</li>
                        <li class="list-group-item"><strong>Cliente:</strong> ${reparacion.cliente}<br>Email: ${reparacion.email}<br>
                        <strong>Problema según el Cliente:</strong> ${reparacion.descripcion}</li>
                        <li class="list-group-item"><strong>Técnico Asignado:</strong> ${reparacion.tecnico}</li>
                    </ul>
                    
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Fecha que Ingresó el Electrodoméstico:</strong> ${reparacion.fechaIng}</li>
                            <li class="list-group-item"><strong>Fecha Envío Presupuesto:</strong> ${reparacion.fechaEnv}</li>
                            <li class="list-group-item"><strong>Fecha que Confirmó el Presupuesto:</strong> ${reparacion.fechaConfirm}</li>
                            <li class="list-group-item"><strong>Arancel Inicial Abonado:</strong> $${reparacion.montoFijo} <strong>Medio de Pago:</strong> ${reparacion.medioPago} <strong>Nro de Comprobante:</strong> ${reparacion.nroCompro}</li>
                            <li class="list-group-item"><strong>Presupuesto Total:</strong> $${reparacion.presupuesto}</li>
                            <li class="list-group-item"><strong>Materiales utilizados:</strong> ${materiales}</li>
                            <li class="list-group-item"><strong>Detalles de la reparación realizada:</strong> ${detalleReparacion}</li>
                            <li class="list-group-item"><strong>Fecha de Fin Estimada:</strong> ${reparacion.fechaFinEst} <strong>Fecha de Fin Real:</strong> ${reparacion.fechaFin}</li>
                            <li class="list-group-item"><strong>Arancel Final Abonado:</strong> $${reparacion.montoFijoFin} <strong>Medio de Pago:</strong> ${reparacion.medioPagoFin} <strong>Nro de Comprobante:</strong> ${reparacion.nroComproFin}</li>
                            <li class="list-group-item"><strong>Fecha de Retiro del Electro:</strong> ${reparacion.fechaCobro}</li>

                            
                            
                        </ul>
            `;
            document.getElementById("overlay").style.display = "flex";
        }


        function cerrarDetalle() {
            document.getElementById("overlay").style.display = "none";
        }
        
    </script>



</body>
    <?php include_once("../footer.php"); ?>
</html>
