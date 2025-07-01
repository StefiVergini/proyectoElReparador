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
                    <li class="list-group-item"><strong>Arancel Inicio Abonado:</strong> $<?= $monto_fijo ?></li>
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
                $dias_quedan = $diferencia->days;
                $dias_quedan = $dias_quedan + 1;
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

                <div class="card-header">Reparación #<?= $rep->getIdReparacion() ?></div>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Fecha Inicio:</strong> <?= $fecha_inicio ?></li>
                    <li class="list-group-item"><strong>Fecha Fin Estimada:</strong> <?= $fecha_est_repa ?></li>
                    <li class="list-group-item"><strong style="color:red;">Quedan <?= $dias_quedan ?> días para terminar la reparación</strong></li>
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
        foreach ($reparaciones as $rep):
            //var_dump($reparaciones);
            if ($rep->getEstadoPresu() == 'Presupuesto a Enviar' && empty($rep->getPresupuesto()) && empty($rep->getFechaEnvioPresup()) && $rep->getConfirmaPresupuesto() == 0):
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
        <?php endif; endforeach; ?>

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
        "fechaIngElectro"         => date("d/m/Y", strtotime($r->getFechaIngElectro())),
        "fechaInicio"             => $r->getFechaInicio(),
        "fechaFinEst"             => $r->getFechaFinEst(),
        "fechaFin"                => $r->getFechaFin(),
        "fechaFinGarantia"        => $r->getFechaFinGarantia(),         // ← nuevo
        "fechaConfirmReparacion"  => $r->getFechaConfirmReparacion(),   // ← nuevo
        "observaciones"           => $r->getObservaciones(),

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
        "medioPagoFin"            => $r->getMedioPagoFin(),
        "nroComproFin"            => $r->getNroComproFin(),             // ← nuevo

        // … puedes agregar más campos si fuera necesario
    ];
}, $reparaciones)) ?>;


function expandirDetalle(id) {
    let overlay = document.getElementById("overlay");
    let contenido = document.getElementById("contenido-reparacion");

    const reparacion = datosReparaciones.find(r => r.id === id);
    if (!reparacion) {
        contenido.innerHTML = "<p>Reparación no encontrada.</p>";
        overlay.style.display = "block";
        return;
    }

    let html = `
        <div class="card-header">Reparación #${reparacion.id}</div>
        <ul class="list-group">
            <li class="list-group-item"><strong>Fecha que ingresó:</strong> ${reparacion.fechaIngElectro}</li>
            <li class="list-group-item"><strong>Electrodoméstico:</strong> <strong>Tipo:</strong> ${reparacion.tipo} |  ${reparacion.marcaModelo} |<strong>N° Serie:</strong> ${reparacion.numSerie}</li>
            <li class="list-group-item"><strong>Cliente:</strong> ${reparacion.cliente.nombre} | <strong>Email:</strong> ${reparacion.cliente.email}<br><strong>Problema según el cliente:</strong> ${reparacion.cliente.problema}</li>
            <li class="list-group-item"><strong>Técnico asignado:</strong> ${reparacion.tecnico}</li>
        </ul>
    `;

    // Presupuesto a Enviar
    if (reparacion.estadoPresu === 'Presupuesto a Enviar' && !reparacion.presupuesto) {
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
    }

    // Esperando confirmación del cliente
    if (reparacion.estadoPresu === 'Presupuesto Enviado' && reparacion.presupuesto && reparacion.confirmaPresupuesto == 0) {
        html += `
            <div class="card mt-2">
                <div class="card-header">Presupuesto enviado</div>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Monto:</strong> $${reparacion.presupuesto}</li>
                    <li class="list-group-item"><strong>Fecha de Envío:</strong> ${reparacion.fechaEnvioPresup || '-'}</li>
                    <li class="list-group-item"><em>Esperando respuesta del cliente...</em></li>
                </ul>
            </div>
        `;
    }

    // Confirmación aceptada - iniciar reparación
    if (reparacion.confirmaPresupuesto == 1 && reparacion.estadoReparacion === 'Pendiente') {
        html += `
            <form action="iniciarReparacion.php" method="POST">
                <input type="hidden" name="id_reparacion" value="${reparacion.id}">
                <div class="card mt-2">
                    <div class="card-header">Reparación Aprobada</div>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Monto aprobado:</strong> $${reparacion.presupuesto}</li>
                        <li class="list-group-item"><strong>Fecha de confirmación:</strong> ${reparacion.fechaEnvioPresup || '-'}</li>
                        <li class="list-group-item centered">
                            <button type="submit" class="btn">Iniciar Reparación</button>
                        </li>
                    </ul>
                </div>
            </form>
        `;
    }

    // Reparación en curso
    if (reparacion.estadoReparacion === 'En Proceso') {
        html += `
            <div class="card mt-2">
                <div class="card-header">Reparación en curso</div>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Fecha de inicio:</strong> ${reparacion.fechaInicio || '-'}</li>
                    <li class="list-group-item"><strong>Observaciones:</strong> ${reparacion.observaciones || 'Ninguna'}</li>
                </ul>
            </div>
        `;
    }

    // Reparación terminada - pasar a cobro
    if (reparacion.estadoReparacion === 'Finalizado' && !reparacion.fechaCobroFin) {
        html += `
            <form action="registrarCobro.php" method="POST">
                <input type="hidden" name="id_reparacion" value="${reparacion.id}">
                <div class="card mt-2">
                    <div class="card-header">Registrar Cobro</div>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <label>Monto final:<br><input type="number" name="monto" value="${reparacion.montoFinRepa || ''}" required></label>
                        </li>
                        <li class="list-group-item">
                            <label>Medio de pago:<br><input type="text" name="medioPago" value="${reparacion.medioPagoFin || ''}" required></label>
                        </li>
                        <li class="list-group-item centered">
                            <button type="submit" class="btn">Registrar</button>
                        </li>
                    </ul>
                </div>
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
    overlay.style.display = "block";
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

        
    </script>


</body>
    <?php include_once("../footer.php"); ?>
</html>
