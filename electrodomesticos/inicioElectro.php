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

        function expandirDetalle(id) {
            let overlay = document.getElementById("overlay");
            let contenido = document.getElementById("contenido-reparacion");

            // Obtener los datos de la reparación seleccionada
            <?php foreach ($reparaciones as $reparacion) : ?>
                if (id === <?= $reparacion->getIdReparacion() ?>) {
                    <?php  $fecha_ing_electro = date("d/m/Y", strtotime($reparacion->getFechaIngElectro()));?>
                    contenido.innerHTML = `
                        <div class="card-header">
                            Reparación #<?= $reparacion->getIdReparacion() ?>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Fecha que Ingresó el Electrodoméstico:</strong> <?= $fecha_ing_electro ?></li>
                            <li class="list-group-item"><strong>Electrodoméstico:</strong> <?= strtoupper($reparacion->getMarca()) . " " . $reparacion->getModelo() ?> <?= "<br>Tipo: ". ucwords($reparacion->getNomTipo()) ."<br>N° de Serie: ". $reparacion->getNumSerie() ?></li>
                            <li class="list-group-item"><strong>Cliente:</strong> <?= "<br>Nombre: ". ucwords($reparacion->getNomCli() . " " . $reparacion->getApeCliente())."<br>Email: ". $reparacion->getEmailCliente(). "<br><strong>Problema según el Cliente: </strong>". ucwords($reparacion->getDescripcion()) ?></li>
                            <li class="list-group-item"><strong>Técnico Asignado:</strong> <?= ucwords($reparacion->getNomTecnico() . " " . $reparacion->getApeTecnico()) ?></li>
                        </ul>
                        <?php if($reparacion->getEstadoPresu() == 'Presupuesto a Enviar' && empty($reparacion->getPresupuesto()) && empty($reparacion->getFechaEnvioPresup()) && $reparacion->getConfirmaPresupuesto() == 0):?>
                            
                            <form action="guardarPresupuesto.php" method="POST">
                                <div class="card-header">
                                    Realizar Presupuesto:
                                </div>
                                <input type="hidden" name="id_reparacion" value="<?= $reparacion->getIdReparacion() ?>">
                                <input type="hidden" name="id_cli" value="<?= $reparacion->getIdCli() ?>">
                                <input type="hidden" name="id_electro" value="<?= $reparacion->getIdElectro() ?>">
                                 <input type="hidden" name="nom_tipo" value="<?= $reparacion->getNomTipo() ?>">
                                <ul class="list-group" style="background:#fdf1b7;">
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
                        <?php elseif ($reparacion->getEstadoPresu() == 'Presupuesto enviado' && !empty($reparacion->getPresupuesto()) && !empty($reparacion->getFechaEnvioPresup()) && $reparacion->getConfirmaPresupuesto() == 0 && empty($reparacion->getFechaInicio())):?>
                            <?php $descPresu = $reparacion->getObservaciones();
                                $partes = explode(" Detalle de la Reparación: ", $descPresu);

                                // La primera parte contiene "Materiales: ..."
                                $materiales = trim(str_replace("Materiales: ", "", $partes[0]));
                                
                                // La segunda parte contiene el detalle de la reparación
                                $detalleReparacion = trim($partes[1]);
                                $detalleReparacion = ucfirst($detalleReparacion);
                                $fecha_envio_presu = date("d/m/Y", strtotime($reparacion->getFechaEnvioPresup()));
                                
                            ?>
                            <form action="guardarRespuestaPresup.php" method="POST">
                                <div class="card-header">
                                    Presupuesto Enviado:
                                </div>
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Fecha de Envío del Presupuesto:</strong> <?= $fecha_envio_presu ?></li>
                                    <li class="list-group-item"><strong> Monto del presupuesto: $</strong> <?= $reparacion->getPresupuesto() ?></li>
                                    <li class="list-group-item"><strong>Materiales a usar:</strong> <?= ucwords($materiales) ?></li>
                                    <li class="list-group-item"><strong>Descripción de la Reparación:</strong> <?= htmlspecialchars($detalleReparacion, ENT_QUOTES, 'UTF-8')  ?></li>
                                    <li class="list-group-item"><strong>Empleado que envió el Presupuesto:</strong> <?= ucwords($reparacion->getNomEmpPresu() . " " . $reparacion->getApeEmpPresu()) ?></li>
                                    <li class="list-group-item centered" id="fecha-fin-container" style="display: none;">
                                    <label class="label" for="fecha_fin_estimada"><strong>Fecha estimada de fin: </strong><input class="input" type="date" name="fecha_fin_estimada"></label>
                                    </li>

                                </ul>
                                <input type="hidden" name="id_reparacion" value="<?= $reparacion->getIdReparacion() ?>">
                                <input type="hidden" name="id_cli" value="<?= $reparacion->getIdCli() ?>">
                                <input type="hidden" name="nom_cli" value="<?= $reparacion->getNomCli() ?>">
                                <input type="hidden" name="email_cli" value="<?= $reparacion->getEmailCliente() ?>">
                                <input type="hidden" name="id_electro" value="<?= $reparacion->getIdElectro() ?>">
                                <input type="hidden" name="marca" value="<?= $reparacion->getMarca() ?>">
                                <input type="hidden" name="modelo" value="<?= $reparacion->getModelo() ?>">
                                <input type="hidden" name="nom_tipo" value="<?= $reparacion->getNomTipo() ?>">
                                <input type="hidden" name="coment_cobro" value="<?= $reparacion->getComentariosCobro() ?>">
                                 <div class="card-header">
                                    ¿Respondió el cliente?
                                </div>
                                <ul class="list-group" style="background:#fdf1b7;">
                                    
                                    <li class="list-group-item centered">
                                        <button name="accion" value="si" class="btn" type="submit" onclick="mostrarFechaFin(event)">Confirma Presupuesto</button> 
                                        <button name="accion" value="no" class="reject-btn btn" type="submit">Rechaza Presupuesto</button>
                                    </li>

                                </ul>   
                            </form>
                        <?php elseif ($reparacion->getEstadoReparacion() == 1 && $reparacion->getConfirmaPresupuesto() == 1 && $reparacion->getEstadoPresu() == 'Presupuesto Confirmado' && !empty($reparacion->getFechaInicio()) ):?>
                            <?php $descPresu = $reparacion->getObservaciones();
                                $partes = explode(" Detalle de la Reparación: ", $descPresu);

                                // La primera parte contiene "Materiales: ..."
                                $materiales = trim(str_replace("Materiales: ", "", $partes[0]));
                                
                                // La segunda parte contiene el detalle de la reparación
                                $detalleReparacion = trim($partes[1]);
                                $fecha_inicio = date("d/m/Y", strtotime($reparacion->getFechaInicio()));
                                $fecha_est_repa = date("d/m/Y", strtotime($reparacion->getFechaFinEst()));
                                $fecha_est_repa_dt = DateTime::createFromFormat('d/m/Y', $fecha_est_repa);

                                $hoy = new DateTime(); // Fecha actual

                                $diferencia = $hoy->diff($fecha_est_repa_dt);
                                $dias_quedan = $diferencia->days;
                                $dias_quedan = $dias_quedan + 1;
                                $presup = $reparacion->getPresupuesto();
                                $monto_fijo = $reparacion->getMontoFijoIni();
                                $cant_a_abonar = $presup - $monto_fijo;
                        ?>
                    
                            <ul class="list-group">
                                <li class="list-group-item centered"><strong> Fecha Inicio:</strong> <?= $fecha_inicio ?> &nbsp; &nbsp; &nbsp; <strong>Fecha Fin Estimada:</strong> <?= $fecha_est_repa ?></li>
                                <li class="list-group-item centered"><strong style="color:red;">Quedan <?= $dias_quedan ?> días para terminar la reparación</strong></li>
                                <li class="list-group-item"><strong>Electrodoméstico:</strong> <?= ucwords($reparacion->getNomTipo()) . " - " . strtoupper($reparacion->getMarca()) ?><br>Modelo: <?= $reparacion->getModelo() ?></li>
                                <li class="list-group-item"><strong>Detalle de la reparación a realizar:</strong> <?=  json_encode($detalleReparacion) ?></li>
                                <li class="list-group-item"><strong>Materiales a utilizar:</strong> <?= $materiales ?></li>
                                <li class="list-group-item centered"><strong>Monto a Abonar Final:</strong> $<strong style="color:black;"><?= $cant_a_abonar ?> </strong> </li>
                                <li class="list-group-item centered">
                                    <button class="btn" type="button" id="retirar-materiales"><a href="retirarMateriales.php" style="text-decoration:none; color:white;">Retirar Materiales</a></button>
                                </li>

                                    
                            </ul> 
                             <form action="finalizarReparacion.php" method="POST">
                                <div class="card-header">
                                    Finalizar Reparación:
                                </div>
                                <input type="hidden" name="id_reparacion" value="<?= $reparacion->getIdReparacion() ?>">
                                
                                <input type="hidden" name="id_cli" value="<?= $reparacion->getIdCli() ?>">
                                <input type="hidden" name="nom_cli" value="<?= $reparacion->getNomCli() ?>">
                                <input type="hidden" name="mail_cli" value="<?= $reparacion->getEmailCliente() ?>">
                                <input type="hidden" name="monto_cobrar" value="<?= $cant_a_abonar ?>"> 
                                <input type="hidden" name="id_electro" value="<?= $reparacion->getIdElectro() ?>">
                                <input type="hidden" name="marca" value="<?= $reparacion->getMarca() ?>">
                                <input type="hidden" name="modelo" value="<?= $reparacion->getModelo() ?>">
                                <input type="hidden" name="nom_tipo" value="<?= $reparacion->getNomTipo() ?>">
                                <ul class="list-group" style="background:#fdf1b7;">
                                <li class="list-group-item centered"><button class="btn" type="submit" id = "btn-finalizar-reparacion">Finalizar Reparación</button></li> 
                                </ul>   
                            </form>
                        <?php elseif ($reparacion->getEstadoReparacion() == 0 && $reparacion->getEstadoPresu() == 'Reparacion Finalizada' && !empty($reparacion->getFechaFin()) && empty($reparacion->getFechaDeRetiro()) && empty($reparacion->getFechaCobroFin()) && empty($reparacion->getMontoFinRepa()) && empty($reparacion->getMedioPagoFin())):?>
                            <?php
                                $fecha_inicio = date("d/m/Y", strtotime($reparacion->getFechaInicio()));
                                $fecha_fin = date("d/m/Y", strtotime($reparacion->getFechaFin()));
                                $presup = $reparacion->getPresupuesto();
                                $monto_fijo = $reparacion->getMontoFijoIni();
                                $cant_a_abonar = $presup - $monto_fijo;
                        ?>
                    
                            <ul class="list-group">
                                <li class="list-group-item centered"><strong> Fecha Inicio:</strong> <?= $fecha_inicio ?> &nbsp; &nbsp; &nbsp; <strong>Fecha Finalizó:</strong> <?= $fecha_fin ?></li>
                                <li class="list-group-item centered"><strong >Monto a Abonar: &nbsp; </strong><strong style="color:red;">$<?= $cant_a_abonar ?></strong></li>
                                <li class="list-group-item"><strong>Electrodoméstico:</strong> <?= ucwords($reparacion->getNomTipo()) . " - " . strtoupper($reparacion->getMarca()) ?><br>Modelo: <?= $reparacion->getModelo() ?></li>
                                    
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
                                <input type="hidden" name="id_reparacion" value="<?= $reparacion->getIdReparacion() ?>">
                                <input type="hidden" name="comentarios" value="<?= $reparacion->getComentariosCobro() ?>">
                                <input type="hidden" name="id_cli" value="<?= $reparacion->getIdCli() ?>">
                                <input type="hidden" name="nom_cli" value="<?= $reparacion->getNomCli() ?>">
                                <input type="hidden" name="mail_cli" value="<?= $reparacion->getEmailCliente() ?>">
                                <input type="hidden" name="monto_a_cobrar" value="<?= $cant_a_abonar ?>"> 
                                <input type="hidden" name="id_electro" value="<?= $reparacion->getIdElectro() ?>">
                                <input type="hidden" name="marca" value="<?= $reparacion->getMarca() ?>">
                                <input type="hidden" name="modelo" value="<?= $reparacion->getModelo() ?>">
                                <input type="hidden" name="nom_tipo" value="<?= $reparacion->getNomTipo() ?>">
                                <ul class="list-group" style="background:#fdf1b7;">
                                <li class="list-group-item centered"><button class="btn" type="submit" id = "cobrar">Cobrar Reparación</button></li> 
                                </ul>   
                            </form>
                        <?php endif;?>
                       
                    `;
                }
            <?php endforeach; ?>
            
            overlay.style.display = "flex";  // Mostrar el overlay centrado
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
