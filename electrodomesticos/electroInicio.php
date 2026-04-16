<?php
include_once("../header.php");
include_once("../conexionPDO.php");
include_once("electro_class.php");

$electro = new Electro($base);
$reparaciones = $electro->leerReparaciones();

$rol_tecnico= $_SESSION['rol'];
//echo $rol_tecnico;
date_default_timezone_set('America/Argentina/Buenos_Aires');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electrodomésticos</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/card.css" />
    <script src="../static/js/funciones_empleados.js"></script>
    <script src="../static/js/funciones_select_nav.js"></script>
    <style>
            .section-btns {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 20px;
            }

            .section-btns button,
            .section-btns form {
                margin: 0;
            }

            .grid-container {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 3fr));
                gap: 40px;
                padding: 40px;
            }
            
            .reject-btn{
                background-color:#f45752;
            }
            .reject-btn:hover{
                background-color: red;
            }
    </style>
</head>
<body>
    <h1 class="titulo">Gestión de Electrodomésticos</h1>
    <hr>
    <br>
    <div class="section-btns">
        <?php if($rol_tecnico == 1 || $rol_tecnico == 7 || $rol_tecnico == 8){?>
            <form action="altaElectro.php" method="post" style="display:none;">
                <button class='btn'>Nueva Reparación</button>
            </form>
        <?php }else{?>
            <form action="altaElectro.php" method="post" style="display:inline;">
                <button class='btn'>Nueva Reparación</button>
            </form>
            
        <?php }?>
        <button class='btn' onclick="mostrarSeccion(event, 'presupuestos')">Presupuestos a Realizar</button>
        <button class='btn' onclick="mostrarSeccion(event, 'respuesta-presup')">Esperando Respuesta</button>
        <button class='btn' onclick="mostrarSeccion(event, 'reparaciones')">Reparaciones a Realizar</button>
        <button class='btn' onclick="mostrarSeccion(event, 'cobros')">Cobrar Reparación</button>
        <button class='btn' onclick="mostrarSeccion(event, 'historial')">Historial de Electrodomésticos</button>

    </div>
    <hr>
     <div id="historial" class="grid-container" style="display:none;">
        <h1 class="titulo" style="grid-column: 1 / -1; text-align:center;">Historial de Electrodomésticos</h1>
        <div class="section-btns" style="grid-column: 1 / -1; text-align:center;">
            <form action="historialReparacionesCobradas.php" method="post" style="display:inline;">
                <button class='btn'>Historial de Reparaciones Cobradas</button>
            </form>
            <form action="historialPresupuestoRechazado.php" method="post" style="display:inline;">
                <button class='btn'>Historial de Presupuestos Rechazados</button>
            </form>
        </div>
    </div>
    <!-- Cobros a realizar -->
    <div id="cobros" class="grid-container" style="display:none;">
        <h1 class="titulo" style="grid-column: 1 / -1; text-align:center;">Cobrar Reparaciones</h1>
        <?php
        $contador_reparaciones = 0;
        foreach ($reparaciones as $rep):
            if ($rep->getEstadoReparacion() == 0 && $rep->getEstadoPresu() == 'Reparacion Finalizada' && !empty($rep->getFechaFin()) && empty($rep->getFechaDeRetiro()) && empty($rep->getFechaCobroFin()) && empty($rep->getMontoFinRepa()) && empty($rep->getMedioPagoFin()) ):  // En reparación
                $contador_reparaciones++;
                $fecha_inicio = date("d/m/Y", strtotime($rep->getFechaInicio()));
                $fecha_est_repa = date("d/m/Y", strtotime($rep->getFechaFinEst()));
                $fecha_fin = date("d/m/Y", strtotime($rep->getFechaFin()));

                $descPresu = $rep->getObservaciones();
                $partes = explode(" Detalle de la Reparación: ", $descPresu);

                // La primera parte contiene "Materiales: ..."
                $materiales = trim(str_replace("Materiales: ", "", $partes[0]));
                                
                // La segunda parte contiene el detalle de la reparación
                $detalleReparacion = trim($partes[1]);
                $presup = $rep->getPresupuesto();
                $monto_fijo = $rep->getMontoFijoIni();
                $cant_a_abonar = $presup - $monto_fijo;
        ?>
            <div class="card" onclick="expandirDetalle(<?= $rep->getIdReparacion() ?>)">

                <div class="card-header">Reparación Finalizada #<?= $rep->getIdReparacion() ?></div>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Monto a Abonar Final:</strong> <strong style="color:red;">$<?= $cant_a_abonar ?> </strong> </li>
                    <li class="list-group-item"><strong>Presupuesto:</strong> $<?= $presup ?></li>
                    <li class="list-group-item"><strong>Arancel Inicial Abonado:</strong> $<?= $monto_fijo ?></li>
                    <li class="list-group-item"><strong>Electrodoméstico:</strong> <?= ucwords($rep->getNomTipo()) . " - " . strtoupper($rep->getMarca()) ?><br>Modelo: <?= $rep->getModelo() ?></li>
                    <li class="list-group-item"><strong>Detalle de la reparación a realizada:</strong> <?= htmlspecialchars($detalleReparacion, ENT_QUOTES, 'UTF-8')   ?></li>
                    <li class="list-group-item"><strong>Materiales a utilizados:</strong> <?= ucwords($materiales) ?></li>
                    <li class="list-group-item centered"><strong>Fecha Inicio:</strong> <?= $fecha_inicio ?> &nbsp; <strong>Fecha Fin:</strong> <?= $fecha_fin ?></li>
                    <li class="list-group-item"><strong>Fecha Fin Estimada:</strong> <?= $fecha_est_repa ?></li>
                    
                    <li class="list-group-item"><strong>Cliente:</strong> <?= ucwords($rep->getNomCli() . " " . $rep->getApeCliente()) ?></li>
                    <li class="list-group-item"><strong>Email Cliente:</strong> <?=$rep->getEmailCliente()?></li>
                    <li class="list-group-item"><strong>Técnico Asignado:</strong> <?= ucwords($rep->getNomTecnico() . " " . $rep->getApeTecnico()) ?></li>
                     <li class="list-group-item" style="text-align: center;"><strong>Cobrar Reparación</strong></li>
                    <li class="list-group-item" style="text-align: center;"><strong>&#9207;</strong></li>
                </ul>
            </div>
        <?php endif; endforeach; ?>

        <?php if ($contador_reparaciones == 0): ?>
            <div style="grid-column: 1 / -1; text-align: center;">
                <h3>No hay Reparaciones para Cobrar</h3>
            </div>
        <?php endif; ?>
    </div>
    <!-- Reparaciones a realizar -->
    <div id="reparaciones" class="grid-container" style="display:none;">
        <h1 class="titulo" style="grid-column: 1 / -1; text-align:center;">Reparaciones a Realizar</h1>
        <?php
        $contador_reparaciones = 0;
        foreach ($reparaciones as $rep):
            if ($rep->getEstadoReparacion() == 1 && $rep->getConfirmaPresupuesto() == 1 && $rep->getEstadoPresu() == 'Presupuesto Confirmado' && !empty($rep->getFechaInicio()) ):  // En reparación
                $contador_reparaciones++;
                $fecha_inicio = date("d/m/Y", strtotime($rep->getFechaInicio()));
                $fecha_est_repa = date("d/m/Y", strtotime($rep->getFechaFinEst()));
                $fecha_est_repa_dt = DateTime::createFromFormat('d/m/Y', $fecha_est_repa);

                $hoy = new DateTime(); // Fecha actual
                $diferencia = $hoy->diff($fecha_est_repa_dt);
                $dias = $diferencia->days + 1; // Incluye el día actual

                if ($diferencia->invert == 0) {
                    // Fecha futura o hoy
                    if ($dias == 1) {
                        $mensaje_dias = "La reparación debería finalizar hoy";
                        $color_mensaje = "blue";
                    } else {
                        $mensaje_dias = "Quedan {$dias} días para terminar la reparación";
                        $color_mensaje = "red";
                    }
                } else {
                    // Fecha pasada
                    if ($dias == 1) {
                        $mensaje_dias = "La reparación debería haber finalizado ayer";
                        $color_mensaje = "orange";
                    } else {
                        $mensaje_dias = "La reparación debería haber finalizado hace {$dias} días";
                        $color_mensaje = "orange";
                    }
                }

               $descPresu = $rep->getObservaciones() ?? "";
               $partes = explode(" Detalle de la Reparación: ", $descPresu);
               $materiales = "";
               $detalleReparacion = "";
               if (count($partes) === 2) {
                    // La primera parte contiene "Materiales: ..."
                    $materiales = trim(str_replace("Materiales: ", "", $partes[0]));
                    // La segunda parte contiene el detalle de la reparación
                    $detalleReparacion = trim($partes[1]);
                }else{
                    $materiales = "-";
                    $detalleReparacion = "-";
                }
                $presup = $rep->getPresupuesto();
                $monto_fijo = $rep->getMontoFijoIni();
                $cant_a_abonar = $presup - $monto_fijo;
        ?>
            <div class="card" onclick="expandirDetalle(<?= $rep->getIdReparacion() ?>)">

                <div class="card-header">Reparación #<?= $rep->getIdReparacion() ?></div>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Fecha Inicio:</strong> <?= $fecha_inicio ?></li>
                    <li class="list-group-item"><strong>Fecha Fin Estimada:</strong> <?= $fecha_est_repa ?></li>
                     <li class="list-group-item"><strong style="color:<?= $color_mensaje ?>;"><?= $mensaje_dias ?></strong></li>
                    <li class="list-group-item"><strong>Electrodoméstico:</strong> <?= ucwords($rep->getNomTipo()) . " - " . strtoupper($rep->getMarca()) ?><br>Modelo: <?= $rep->getModelo() ?></li>
                    <li class="list-group-item"><strong>Detalle de la reparación a realizar:</strong> <?= htmlspecialchars($detalleReparacion, ENT_QUOTES, 'UTF-8')   ?></li>
                    <li class="list-group-item"><strong>Materiales a utilizar:</strong> <?= $materiales ?></li>
                    <li class="list-group-item"><strong>Presupuesto:</strong> $<?= $presup ?></li>
                    <li class="list-group-item"><strong>Arancel Inicio Abonado:</strong> $<?= $monto_fijo ?></li>
                    <li class="list-group-item"><strong>Monto a Abonar Final:</strong> $<strong style="color:black;"><?= $cant_a_abonar ?> </strong> </li>
                    <li class="list-group-item"><strong>Cliente:</strong> <?= ucwords($rep->getNomCli() . " " . $rep->getApeCliente()) ?></li>
                    <li class="list-group-item"><strong>Técnico Asignado:</strong> <?= ucwords($rep->getNomTecnico() . " " . $rep->getApeTecnico()) ?></li>
                    <li class="list-group-item" style="text-align: center;"><strong>&#9207;</strong></li>
                </ul>
            </div>
        <?php endif; endforeach; ?>

        <?php if ($contador_reparaciones == 0): ?>
            <div style="grid-column: 1 / -1; text-align: center;">
                <h3>No hay reparaciones activas</h3>
            </div>
        <?php endif; ?>
    </div>

    <!-- Presupuestos a realizar -->
    <div id="presupuestos" class="grid-container" style="display:none;">
        <h1 class="titulo" style="grid-column: 1 / -1; text-align:center;">Presupuestos a Realizar</h1>
        <?php
        $contador_presupuestos = 0;
        $idsMostrados = [];
        foreach ($reparaciones as $rep){
            if (in_array($rep->getIdReparacion(), $idsMostrados)) {
                continue; // ya se mostró la card, entonces lo saltea
            }
            //var_dump($reparaciones);
            if ($rep->getEstadoPresu() == 'Presupuesto a Enviar' && empty($rep->getPresupuesto()) && empty($rep->getFechaEnvioPresup()) && $rep->getConfirmaPresupuesto() == 0){
                $contador_presupuestos++;
                $fecha_ing_electro = date("d/m/Y", strtotime($rep->getFechaIngElectro()));
            ?>
                <div class="card" onclick="expandirDetalle(<?= $rep->getIdReparacion() ?>)">
                    <div class="card-header">Presupuesto pendiente - Reparación #<?= $rep->getIdReparacion() ?></div>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Fecha que Ingresó el Electrodoméstico:</strong> <?= $fecha_ing_electro ?></li>
                        <li class="list-group-item"><strong>Electrodoméstico:</strong> <?= ucwords($rep->getNomTipo()) . " - " . strtoupper($rep->getMarca()) ?><br>Modelo: <?= $rep->getModelo() ?></li>
                        <li class="list-group-item"><strong>Cliente:</strong> <?= ucwords($rep->getNomCli() . " " . $rep->getApeCliente()) ?></li>
                        <li class="list-group-item"><strong>Técnico Asignado:</strong> <?= ucwords($rep->getNomTecnico() . " " . $rep->getApeTecnico()) ?></li>
                        <li class="list-group-item" style="text-align: center;"><strong>&#9207;</strong></li>
                    </ul>
                </div>
            <?php } elseif ($rep->getEstadoPresu() == 'Reparacion por Garantia' && $rep->getConfirmaPresupuesto() == 0){
                    $contador_presupuestos++;
                    $fecha_ing_electro = date("d/m/Y", strtotime($rep->getFechaIngElectro()));
                    $idCli = $rep->getIdCli();
                    $idAnt = $rep->getDescReparacion();
                    $electroXCli = $electro->filtrarHistorialReparaciones('','','',$idCli); 
                    $electroFiltrados = [];
                    foreach ($electroXCli as $e) {
                        $estadoPresup = $e->getEstadoPresu(); // estado_presup del presupuesto
                        $idRepa = $e->getIdReparacion();

                        if ($estadoPresup === 'Reparacion Cobrada' && $idRepa == $idAnt){
                            $electroFiltrados[] = $e;
                        }
                    }
                    if (!empty($electroFiltrados)) {
                        $e = $electroFiltrados[0]; // tomás el primero
                        $datoBase["anterior"]  = [
                            "idAnt" => $e->getIdReparacion(),
                            "idElectroAnt" => $e->getIdElectro(),
                            "idCliAnt" => $e->getIdCli(),
                            "fechaFinGarantiaAnt" => $e->getFechaFinGarantia(),
                            "observacionesAnt" => $e->getObservaciones(),
                        ];
                    }
                    ?>
                        <div class="card" onclick="expandirDetalle(<?= $rep->getIdReparacion() ?>)">
                            <div class="card-header">Garantía Activa - Reparación #<?= $rep->getIdReparacion() ?></div>
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Fecha que Ingresó el Electrodoméstico:</strong> <?= $fecha_ing_electro ?></li>
                                <li class="list-group-item"><strong>Electrodoméstico:</strong> <?= ucwords($rep->getNomTipo()) . " - " . strtoupper($rep->getMarca()) ?><br>Modelo: <?= $rep->getModelo() ?></li>
                                <li class="list-group-item"><strong>Cliente:</strong> <?= ucwords($rep->getNomCli() . " " . $rep->getApeCliente()) ?></li>
                                <li class="list-group-item"><strong>Técnico Asignado:</strong> <?= ucwords($rep->getNomTecnico() . " " . $rep->getApeTecnico()) ?></li>
                                <li class="list-group-item" style="text-align: center;"><strong>&#9207;</strong></li>
                            </ul>
                        </div>
                <?php 
                }
            $idsMostrados[] = $rep->getIdReparacion(); 
        } ?>

        <?php if ($contador_presupuestos == 0): ?>
            <div style="grid-column: 1 / -1; text-align: center;">
                <h3>No hay presupuestos pendientes</h3>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Esperando Respuesta de Presupuesto -->
    <div id="respuesta-presup" class="grid-container" style="display:none;">
        <h1 class="titulo" style="grid-column: 1 / -1; text-align:center;">Esperando Respuesta de Presupuesto</h1>
        <?php
        $contador_respuesta = 0;
        foreach ($reparaciones as $rep):
            //var_dump($reparaciones);
            if ($rep->getEstadoPresu() == 'Presupuesto enviado' && !empty($rep->getPresupuesto()) && !empty($rep->getFechaEnvioPresup()) && $rep->getConfirmaPresupuesto() == 0 && empty($rep->getFechaInicio())):
                $contador_respuesta++;
                $fecha_ing_electro = date("d/m/Y", strtotime($rep->getFechaIngElectro()));
                $fecha_env_presup = date("d/m/Y", strtotime($rep->getFechaEnvioPresup()));
                $fecha_env_presup_dt = DateTime::createFromFormat('d/m/Y', $fecha_env_presup);

                $hoy = new DateTime(); // Fecha actual

                $diferencia = $fecha_env_presup_dt->diff($hoy);
                $dias_transcurridos = $diferencia->days;

        ?>
            <div class="card" onclick="expandirDetalle(<?= $rep->getIdReparacion() ?>)">
                <div class="card-header">Presupuesto enviado - Reparación #<?= $rep->getIdReparacion() ?></div>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Fecha de Envío del Presupuesto:</strong> <?= $fecha_env_presup ?> <strong>Hace:</strong> <?= $dias_transcurridos ?><strong> días</strong>  </li>
                    <li class="list-group-item"><strong>Fecha que Ingresó el Electrodoméstico:</strong> <?= $fecha_ing_electro ?></li>
                    <li class="list-group-item"><strong>Electrodoméstico:</strong> <?= ucwords($rep->getNomTipo()) . " - " . strtoupper($rep->getMarca()) ?><br>Modelo: <?= $rep->getModelo() ?></li>
                    <li class="list-group-item"><strong>Cliente:</strong> <?= ucwords($rep->getNomCli() . " " . $rep->getApeCliente()) ?></li>
                    <li class="list-group-item"><strong>Técnico Asignado:</strong> <?= ucwords($rep->getNomTecnico() . " " . $rep->getApeTecnico()) ?></li>
                    <li class="list-group-item" style="text-align: center;"><strong>Datos del Presupuesto</strong></li>
                    <li class="list-group-item" style="text-align: center;"><strong>&#9207;</strong></li>
                </ul>
            </div>
        <?php endif; endforeach; ?>

        <?php if ($contador_respuesta == 0): ?>
            <div style="grid-column: 1 / -1; text-align: center;">
                <h3>No hay Respuestas pendientes de Confirmación</h3>
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
    

    <script>

        function mostrarSeccion(event, seccionId) {
            event.preventDefault();

            var reparaciones = document.getElementById("reparaciones");
            var presupuestos = document.getElementById("presupuestos");
            var respuesta = document.getElementById("respuesta-presup");
            var cobros = document.getElementById("cobros");
            var historial = document.getElementById("historial");
            

            // Ocultar
            reparaciones.style.display = "none";
            presupuestos.style.display = "none";
            respuesta.style.display = "none";
            cobros.style.display = "none";
            historial.style.display="none";

            // Mostrar el seleccionado
            var seccion = document.getElementById(seccionId);
            seccion.style.display = "grid";
        }

        // Al cargar la página, ocultar 
        window.addEventListener("load", function () {
            document.getElementById("reparaciones").style.display = "none";
            document.getElementById("presupuestos").style.display = "none";
            document.getElementById("respuesta-presup").style.display = "none";
            document.getElementById("cobros").style.display = "none";
            document.getElementById("historial").style.display = "none";
        });


        
        //Expandir tarjetas

     // Paso 1: preparar el array PHP con toda la info necesaria para todas las reparaciones
    const datosReparaciones = <?= json_encode(array_map(function($r) {
    return [
        // —————— Datos básicos ——————
        "id"                      => $r->getIdReparacion(),
        "idElectro"               => $r->getIdElectro(),
        "marcaModelo"             => strtoupper($r->getMarca()) . " " . $r->getModelo(),
        "tipoElectro"             => $r->getTipoElectro(),             // ← nuevo
        "tipo"                    => ucwords($r->getNomTipo()),
        "numSerie"                => $r->getNumSerie(),
        "edComent"                => $r->getEdComentario(),

        // —————— Cliente ——————
        "idCli"                   => $r->getIdCli(),
        "cliente" => [
            "nombre"              => ucwords($r->getNomCli() . " " . $r->getApeCliente()),
            "email"               => $r->getEmailCliente(),
            "problema"            => ucwords($r->getDescripcion())
        ],

        // —————— Técnico ——————
        "idTecnico"               => $r->getIdTecnico(),                // ← nuevo
        "tecnico"                 => ucwords($r->getNomTecnico() . " " . $r->getApeTecnico()),

        // —————— Presupuesto ——————
        "estadoPresu"             => $r->getEstadoPresu(),
        "presupuesto"             => $r->getPresupuesto(),
        "fechaEnvioPresup"        => $r->getFechaEnvioPresup(),
        "confirmaPresupuesto"     => $r->getConfirmaPresupuesto(),

        // —————— Reparación ——————
        "estadoReparacion"        => $r->getEstadoReparacion(),
        "fechaIngElectro"         => $r->getFechaIngElectro(),
        "fechaInicio"             => $r->getFechaInicio(),
        "fechaFinEst"             => $r->getFechaFinEst(),
        "fechaFin"                => $r->getFechaFin(),
        "fechaFinGarantia"        => $r->getFechaFinGarantia(),         // ← nuevo
        "fechaConfirmReparacion"  => $r->getFechaConfirmReparacion(),   // ← nuevo
        "observaciones"           => $r->getObservaciones(),
        "descReparacion"          => $r->getDescReparacion(),

        // —————— Empleado de atención y presupuesto ——————
        "idEmpAtencion"           => $r->getIdEmpAtencion(),            // ← nuevo
        "nomEmpAtencion"          => $r->getNomEmpAtencion(),           // ← nuevo
        "apeEmpAtencion"          => $r->getApeEmpAtencion(),           // ← nuevo
        "idEmpPresu"              => $r->getIdEmpPresu(),               // ← nuevo
        "nomEmpPresu"             => $r->getNomEmpPresu(),
        "apeEmpPresu"             => $r->getApeEmpPresu(),

        // —————— Cobros ——————
        "idCobro"                 => $r->getIdCobro(),                  // ← nuevo
        "montoFijoIni"            => $r->getMontoFijoIni(),
        "fechaCobroIni"           => $r->getFechaCobroIni(),            // ← nuevo
        "nroComproIni"            => $r->getNroComproIni(),             // ← nuevo
        "medioPagoIni"            => $r->getMedioPagoIni(),             // ← nuevo
        "comentariosCobro"        => $r->getComentariosCobro(),
        "montoFinRepa"            => $r->getMontoFinRepa(),
        "fechaCobroFin"           => $r->getFechaCobroFin(),
        "fechaRetiro"             => $r->getFechaDeRetiro(),
        "medioPagoFin"            => $r->getMedioPagoFin(),
        "nroComproFin"            => $r->getNroComproFin(),             // ← nuevo

       
    ];
}, $reparaciones)) ?>;

function parseObservaciones(observaciones) {
    let descPresu = observaciones || "";
    let partes = descPresu.split(" Detalle de la Reparación: ");

    let materiales = "";
    let detalleReparacion = "";

    if (partes.length === 2) {
        materiales = partes[0].replace("Materiales: ", "").trim();
        detalleReparacion = partes[1].trim();
        detalleReparacion = detalleReparacion.charAt(0).toUpperCase() + detalleReparacion.slice(1);
    }

    return {
        materiales,
        detalleReparacion
    };
}
function formatearFecha(fecha) {
    if (!fecha) return "-";
    
    let d = new Date(fecha);

    // se suma un día porque js siempre reconoce mal la fecha acá
    d.setDate(d.getDate() + 1);

    return d.toLocaleDateString("es-AR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric"
    });
}
const datosRepaAnt = <?= json_encode(array_map(function($ant) {
    return [
        "idAnt"          => $ant->getIdReparacion(),
        "idElectroAnt"      => $ant->getIdElectro(),
        "idCliAnt"          => $ant->getIdCli(),
        "fechaFinGarantiaAnt" => $ant->getFechaFinGarantia(),
        "observacionesAnt"  => $ant->getObservaciones(),
    ];
}, $electroFiltrados ?? [])) ?>;

function expandirDetalle(id) {
    let overlay = document.getElementById("overlay");
    let contenido = document.getElementById("contenido-reparacion");
    
    const reparacion = datosReparaciones.find(r => r.id === id);
  
    let fechaIngElectro    = formatearFecha(reparacion.fechaIngElectro);
    let fechaEnvioPresup    = formatearFecha(reparacion.fechaEnvioPresup);
    let fechaIni    = formatearFecha(reparacion.fechaInicio);
    let fechaFin   = formatearFecha(reparacion.fechaFin);
    let fechaFinGarantia   = formatearFecha(reparacion.fechaFinGarantia);
    let fechaConfirmReparacion = formatearFecha(reparacion.fechaConfirmReparacion);
    let fechaCobroIni = formatearFecha(reparacion.fechaCobroIni);
    let fechaCobroFin = formatearFecha(reparacion.fechaCobroFin);
    let fechaRetiro = formatearFecha(reparacion.fechaRetiro);
    let fechaFinEstimada = formatearFecha(reparacion.fechaFinEst);

    let html = `
        <div class="card-header">Reparación #${reparacion.id}</div>
        <ul class="list-group">
            <li class="list-group-item"><strong>Fecha que ingresó:</strong> ${fechaIngElectro}</li>
            <li class="list-group-item"><strong>Electrodoméstico:</strong> <strong>Tipo:</strong> ${reparacion.tipo} |  ${reparacion.marcaModelo} |<strong>N° Serie:</strong> ${reparacion.numSerie}</li>
            <li class="list-group-item"><strong>Cliente:</strong> ${reparacion.cliente.nombre} | <strong>Email:</strong> ${reparacion.cliente.email}<br><strong>Problema según el cliente:</strong> ${reparacion.cliente.problema}</li>
            <li class="list-group-item"><strong>Técnico asignado:</strong> ${reparacion.tecnico}</li>
            <li class="list-group-item"><strong>Monto Inicial Abonado:</strong>$ ${reparacion.montoFijoIni}<br> <strong>Nro de Comprobante:</strong>${reparacion.nroComproIni}<br> <strong>Medio de Pago:</strong> ${reparacion.medioPagoIni}</li>
        </ul>
    `;
    // Presupuesto a Enviar
    if ((reparacion.estadoPresu === 'Presupuesto a Enviar' && !reparacion.presupuesto) || (reparacion.estadoPresu === 'Reparacion por Garantia')) {
        if (reparacion.estadoPresu === 'Presupuesto a Enviar' && !reparacion.presupuesto){

            html += `
                <form action="guardarPresupuesto.php" method="POST">
                    <input type="hidden" name="id_reparacion" value="${reparacion.id}">
                    <input type="hidden" name="id_cli" value="${reparacion.idCli}">
                    <input type="hidden" name="id_electro" value="${reparacion.idElectro}">
                    <input type="hidden" name="nom_tipo" value="${reparacion.tipo}">
                    <div class="card-header">Realizar Presupuesto</div>
                    <ul class="list-group">
                        <li class="list-group-item centered">
                            <strong><label class="label" for="materiales">Materiales a utilizar:<textarea class="input"  name="materiales" id="materiales" cols="40" rows="10"></textarea></label></strong>
                        </li>
                        <li class="list-group-item centered">
                            <strong><label class="label" for="descRePresu">Descripción de la Reparación:<textarea class="input"  name="descRePresu" id="descRePresu" cols="40" rows="10"></textarea></label></strong>
                        </li>
                        <li class="list-group-item centered">
                            <strong><label class="label" for="presup">Presupuesto total:<input class="input" type="number" name="presup" placeholder="$" required></label></strong>
                        </li>
                        <li class="list-group-item centered"><button class="btn" type="submit">Enviar Presupuesto</button></li> 
                    </ul>
                </form>
            `;
        } else{

            const reparacionAnterior = datosRepaAnt[0];

            const obsAnterior = reparacionAnterior?.observacionesAnt || "";
            const { materiales, detalleReparacion } = parseObservaciones(obsAnterior);
            html += `
                <form action="garantiaAceptadaORechazada.php" method="POST">
                    <div class="card-header">Reparación Anterior del Electro:</div>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Id Reparación Anterior:</strong> ${reparacionAnterior.idAnt}</li>
                        <li class="list-group-item"><strong>Fin Garantía:</strong> ${formatearFecha(reparacionAnterior.fechaFinGarantiaAnt)}</li>
                        <li class="list-group-item"><strong>Materiales usados:</strong> ${materiales}</li>
                        <li class="list-group-item"><strong>Descripción:</strong> ${detalleReparacion}</li>
                    </ul>
                    <div class="card-header">Detalle de la Reparación:</div>
                    <ul class="list-group">
                        <li class="list-group-item centered">
                            <strong><label class="label" for="materiales">Materiales a utilizar:<textarea class="input"  name="materiales" id="materiales" cols="40" rows="10"></textarea></label></strong>
                        </li>
                        <li class="list-group-item centered">
                            <strong><label class="label" for="descRePresu">Descripción de la Reparación:<textarea class="input"  name="descRePresu" id="descRePresu" cols="40" rows="10"></textarea></label></strong>
                        </li>
                        <li class="list-group-item centered" id="fecha-fin-container" style="display: none;">
                            <label class="label" for="fecha_fin_estimada">
                                <strong>Fecha estimada de fin: </strong>
                                <input class="input" type="date" name="fecha_fin_estimada">
                            </label>
                        </li>
                    </ul>
                    
                    
                    <div class="card-header" id="titulo-noCubre" style="display: none;">Datos - Garantía Rechazada:</div>
                    <ul class="list-group">
                        <li class="list-group-item centered" id="xqNoCubre" style="display: none;">
                            <label class="label" for="xqNoCubre"><strong>Explique por qué no cubre la Garantía:</strong><textarea class="input"  name="xqNoCubre" id="xqNoCubre" cols="40" rows="10"></textarea></label>
                        </li>
                        <li class="list-group-item centered" id="presup" style="display: none;">
                            <label class="label" for="presup"><strong>Presupuesto total:</strong><input class="input" type="number" name="presup" placeholder="$"</label>
                        </li>
                    </ul>
                    <!-- Campos ocultos -->
                    <input type="hidden" name="id_reparacion" value="${reparacion.id}">
                    <input type="hidden" name="id_cli" value="${reparacion.idCli}">
                    <input type="hidden" name="nom_cli" value="${reparacion.cliente?.nombre || ''}">
                    <input type="hidden" name="email_cli" value="${reparacion.cliente?.email || ''}">
                    <input type="hidden" name="id_electro" value="${reparacion.idElectro}">
                    <input type="hidden" name="marca" value="${reparacion.marcaModelo?.split(" ")[0] || ''}">
                    <input type="hidden" name="modelo" value="${reparacion.marcaModelo?.split(" ")[1] || ''}">
                    <input type="hidden" name="nom_tipo" value="${reparacion.tipo || ''}">
                    <input type="hidden" name="tecnico" value="${reparacion.idTecnico}">
                    

                    <div class="card-header"> Una vez analizado el Electro, ¿cubre la garantía?</div>
                    <ul class="list-group" style="background:#fdf1b7;">
                        <li class="list-group-item centered">
                            <button name="accion" value="si" class="btn" type="submit" onclick="mostrarFechaFin(event)">
                                Sí - Reparar Electro
                            </button> 
                            <button name="accion" value="no" class="reject-btn btn" type="submit" onclick="mostrarDatosNoCubreGarant(event)">
                                No - Enviar Presupuesto
                            </button>
                        </li>
                    </ul>                    
                </form>
            `;
        }
    }
    
   // Esperando confirmación del cliente
    else if (reparacion.estadoPresu === 'Presupuesto enviado' && reparacion.presupuesto && reparacion.confirmaPresupuesto == 0) {
        let { materiales, detalleReparacion } = parseObservaciones(reparacion.observaciones);
        // --------------------
        // Formulario con toda la info y botones
        // --------------------
        html += `
            <form action="guardarRespuestaPresup.php" method="POST">
                <div class="card-header">Presupuesto Enviado:</div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Fecha de Envío del Presupuesto:</strong> ${fechaEnvioPresup || "-"}
                    </li>
                    <li class="list-group-item">
                        <strong>Monto del presupuesto: $</strong> ${reparacion.presupuesto}
                    </li>
                    <li class="list-group-item">
                        <strong>Materiales a usar:</strong> ${materiales || "-"}
                    </li>
                    <li class="list-group-item">
                        <strong>Descripción de la Reparación:</strong> ${detalleReparacion || "-"}
                    </li>
                    <li class="list-group-item">
                        <strong>Empleado que envió el Presupuesto:</strong> ${reparacion.nomEmpPresu} ${reparacion.apeEmpPresu}
                    </li>
                    <li class="list-group-item centered" id="fecha-fin-container" style="display: none;">
                        <label class="label" for="fecha_fin_estimada">
                            <strong>Fecha estimada de fin: </strong>
                            <input class="input" type="date" name="fecha_fin_estimada">
                        </label>
                    </li>
                </ul>

                <!-- Campos ocultos -->
                <input type="hidden" name="id_reparacion" value="${reparacion.id}">
                <input type="hidden" name="id_cli" value="${reparacion.idCli}">
                <input type="hidden" name="nom_cli" value="${reparacion.cliente?.nombre || ''}">
                <input type="hidden" name="email_cli" value="${reparacion.cliente?.email || ''}">
                <input type="hidden" name="id_electro" value="${reparacion.idElectro}">
                <input type="hidden" name="marca" value="${reparacion.marcaModelo?.split(" ")[0] || ''}">
                <input type="hidden" name="modelo" value="${reparacion.marcaModelo?.split(" ")[1] || ''}">
                <input type="hidden" name="nom_tipo" value="${reparacion.tipo || ''}">
                <input type="hidden" name="coment_cobro" value="${reparacion.comentariosCobro || ''}">

                <div class="card-header">¿Respondió el cliente?</div>
                <ul class="list-group" style="background:#fdf1b7;">
                    <li class="list-group-item centered">
                        <button name="accion" value="si" class="btn" type="submit" onclick="mostrarFechaFin(event)">
                            Confirma Presupuesto
                        </button> 
                        <button name="accion" value="no" class="reject-btn btn" type="submit">
                            Rechaza Presupuesto
                        </button>
                    </li>
                </ul>
            </form>
        `;
    }
    // Confirmación aceptada - iniciar reparación
    else if (reparacion.confirmaPresupuesto == 1 && reparacion.estadoPresu === 'Presupuesto Confirmado') {
       let { materiales, detalleReparacion } = parseObservaciones(reparacion.observaciones);

        let fechaInicio = new Date(reparacion.fechaInicio);
        let fechaEstRepa = new Date(reparacion.fechaFinEst);

        // Fecha actual
        let hoy = new Date();
        hoy.setDate(hoy.getDate() + 1);
        // Calculamos la diferencia en milisegundos
        let diffMs = fechaEstRepa - hoy; // positivo si fecha futura
        let diffDias = Math.ceil(diffMs / (1000 * 60 * 60 * 24)); // convertir ms a días, redondeando hacia arriba

        let mensajeDias = "";
        let colorMensaje = "";

        if (diffDias >= 0) {
            // Fecha futura o hoy
            if (diffDias === 0) {
                mensajeDias = "La reparación debería finalizar hoy";
                colorMensaje = "blue";
            } else {
                mensajeDias = `Quedan ${diffDias} días para terminar la reparación`;
                colorMensaje = "red";
            }
        } else {
            // Fecha pasada
            let diasPasados = Math.abs(diffDias);
            if (diasPasados === 1) {
                mensajeDias = "La reparación debería haber finalizado ayer";
                colorMensaje = "orange";
            } else {
                mensajeDias = `La reparación debería haber finalizado hace ${diasPasados} días`;
                colorMensaje = "orange";
            }
        }

        let cant_a_abonar = (reparacion.presupuesto) - (reparacion.montoFijoIni);
       
        html += `
            <form action="finalizarReparacion.php" method="POST">
                <input type="hidden" name="id_reparacion" value="${reparacion.id}">
            
                <div class="card-header">Reparación Aprobada</div>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Presupuesto Confirmado:</strong> $${reparacion.presupuesto}</li>
                    <li class="list-group-item"><strong>Fecha de confirmación:</strong> ${fechaConfirmReparacion || '-'}</li>
                    <li class="list-group-item centered"><strong>Monto a Abonar Final:</strong> $<strong style="color:black;">${cant_a_abonar} </strong> </li>
                    <li class="list-group-item centered"><strong> Fecha Inicio:</strong> ${fechaIni} &nbsp; &nbsp; &nbsp; <strong>Fecha Fin Estimada:</strong>${fechaFinEstimada}</li>
                    <li class="list-group-item centered"><strong style="color:${colorMensaje}">${mensajeDias}</strong></li>
                    <li class="list-group-item">
                        <strong>Materiales a usar:</strong> ${materiales || "-"}
                    </li>
                    <li class="list-group-item">
                        <strong>Descripción de la Reparación:</strong> ${detalleReparacion || "-"}
                    </li>
                    <li class="list-group-item">
                        <strong>Empleado que envió el Presupuesto:</strong> ${reparacion.nomEmpPresu} ${reparacion.apeEmpPresu}
                    </li>
                    
                    <li class="list-group-item centered">
                    <button class="btn" type="button" id="retirar-materiales"><a href="retirarMateriales.php" style="text-decoration:none; color:white;">Retirar Materiales</a></button>
                    </li>
                </ul>
            
            </form>

              </ul> 
            <form action="finalizarReparacion.php" method="POST">
                <div class="card-header">
                    Finalizar Reparación:
                </div>
                <input type="hidden" name="id_reparacion" value=" ${reparacion.id}">
                
                <input type="hidden" name="id_reparacion" value="${reparacion.id}">
                <input type="hidden" name="id_cli" value="${reparacion.idCli}">
                <input type="hidden" name="nom_cli" value="${reparacion.cliente?.nombre || ''}">
                <input type="hidden" name="mail_cli" value="${reparacion.cliente?.email || ''}">
                <input type="hidden" name="id_electro" value="${reparacion.idElectro}">
                <input type="hidden" name="marca" value="${reparacion.marcaModelo?.split(" ")[0] || ''}">
                <input type="hidden" name="modelo" value="${reparacion.marcaModelo?.split(" ")[1] || ''}">
                <input type="hidden" name="nom_tipo" value="${reparacion.tipo || ''}">
                <input type="hidden" name="monto_cobrar" value=" ${cant_a_abonar}"> 

                <ul class="list-group" style="background:#fdf1b7;">
                <li class="list-group-item centered"><button class="btn" type="submit" id = "btn-finalizar-reparacion">Finalizar Reparación</button></li> 
                </ul>   
        </form>
        `;
    }

    // Reparación terminada - pasar a cobro
    if (reparacion.estadoPresu === 'Reparacion Finalizada' && !reparacion.fechaCobroFin && !reparacion.fechaDeRetiro && !reparacion.montoFinRepa) {
        let monto_a_abonar = (reparacion.presupuesto) - (reparacion.montoFijoIni);
        html += `
            <ul class="list-group">
                 <li class="list-group-item centered"><strong> Fecha Inicio:</strong> ${fechaIni} &nbsp; &nbsp; &nbsp; <strong>Fecha Finalizó:</strong> ${fechaFin}</li>
                <li class="list-group-item centered"><strong >Monto a Abonar: &nbsp; </strong><strong style="color:red;">${monto_a_abonar}</strong></li>                
                                    
            </ul> 
            <form action="cobrarReparacion.php" method="POST">
                <div class="card-header">
                    Cobrar Reparación:
                </div>
                    <ul class="list-group" style="background:#fdf1b7;">
                    <li class="list-group-item centered">
                        <strong><label class="label" for="monto_abona">Monto que Abona:<input class="input" type="number" name="monto_abona" placeholder="$" required></label></strong>
                    </li>
                    <li class="list-group-item centered">
                        <strong><label class="label" for="medio_pago" >Medio de Pago</label>
                        <input type="radio" name="medio_pago" value="efectivo" required onclick="mostrarComprobante(this.value)">
                        <label for="efectivo">Efectivo</label><br>
                        <input type="radio" name="medio_pago" value="transferencia" required onclick="mostrarComprobante(this.value)">
                        <label for="transferencia">Transferencia Bancaria</label></strong>
                    </li>
                    <li class="list-group-item centered">
                        <div class="form-group" id="comprobante-container" style="display: none;">
                            <label class="label" for="nro_comprobante">N° de Comprobante</label>
                            <input type="text" class="input" name="nro_comprobante" id="nro_comprobante">
                        </div>
                    </li>
                    <li class="list-group-item centered">
                        <strong><label class="label" for="coment_cobro">Algún comentario sobre el cobro:<textarea class="input"  name="coment_cobro" id="coment_cobro" cols="40" rows="10"></textarea></label></strong>
                    </li>
                </ul>   
                <input type="hidden" name="id_reparacion" value="${reparacion.id}">
                <input type="hidden" name="comentarios" value="${reparacion.comentariosCobro}">
                <input type="hidden" name="id_cli" value="${reparacion.idCli}">
                 <input type="hidden" name="nom_cli" value="${reparacion.cliente?.nombre || ''}">
                <input type="hidden" name="mail_cli" value="${reparacion.cliente?.email || ''}">
                <input type="hidden" name="id_electro" value="${reparacion.idElectro}">
                <input type="hidden" name="marca" value="${reparacion.marcaModelo?.split(" ")[0] || ''}">
                <input type="hidden" name="modelo" value="${reparacion.marcaModelo?.split(" ")[1] || ''}">
                <input type="hidden" name="nom_tipo" value="${reparacion.tipo || ''}">
                <input type="hidden" name="monto_a_cobrar" value="${monto_a_abonar}">
                <ul class="list-group" style="background:#fdf1b7;">
                <li class="list-group-item centered"><button class="btn" type="submit" id = "cobrar">Cobrar Reparación</button></li> 
                </ul>   
            </form>
        `;
    }

    // Retiro completado
    if (reparacion.fechaDeRetiro) {
        html += `
            <div class="card mt-2">
                <div class="card-header">Reparación retirada</div>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Fecha de retiro:</strong> ${reparacion.fechaDeRetiro}</li>
                    <li class="list-group-item"><strong>Monto final cobrado:</strong> $${reparacion.montoFinRepa || '-'}</li>
                    <li class="list-group-item"><strong>Medio de pago:</strong> ${reparacion.medioPagoFin || '-'}</li>
                </ul>
            </div>
        `;
    }

    contenido.innerHTML = html;
    overlay.style.display = "flex";
}

        function cerrarDetalle() {
            document.getElementById("overlay").style.display = "none";
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
        function mostrarFechaFin(event) {
            const fechaFinContainer = document.getElementById("fecha-fin-container");
            const fechaFinInput = fechaFinContainer.querySelector("input");

            // Si el campo de fecha ya tiene un valor, permite el envío del formulario
            if (fechaFinInput.value) {
                return true; // El formulario se enviará normalmente
            }

            // Evita el envío solo si el campo aún no tiene un valor
            event.preventDefault();
            fechaFinContainer.style.display = "block";
            fechaFinInput.setAttribute("required", "true");


        }
        function mostrarDatosNoCubreGarant(event){
            event.preventDefault(); 

            const titulo = document.getElementById("titulo-noCubre");
            const presup = document.getElementById("presup");
            const presupInput = presup?.querySelector("input");
            const noCubre = document.getElementById("xqNoCubre");
            const noCubreTextArea = noCubre?.querySelector("textarea");

            // Mostrar los campos ocultos
            if (titulo) titulo.style.display = "block";
            if (presup) presup.style.display = "block";
            if (noCubre) noCubre.style.display = "block";

            // Hacerlos requeridos
            if (presupInput) presupInput.required = true;
            if (noCubreTextArea) noCubreTextArea.required = true;

            // Opcional: enfocar el primer campo
            noCubreTextArea?.focus();
        }
        
    </script>


</body>
    <?php include_once("../footer.php"); ?>
</html>
