<?php
include("../header.php");
include("../conexionPDO.php");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Eventos</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/calendario.css" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.js"></script>
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
<main>
    <h1 style="text-align: center; color: #1d4b9f;">CALENDARIO</h1>
    <div class="div-calendario">
        <div class="lista-eventos">
            <div class="lista-eventos-header">Próximos Eventos</div>
            <div class="agregar-evento-btn"><img src="../static/images/agregar.png" alt="Nuevo Evento" width='20' height='20'></div>
            <div id="event-list-container"></div>
        </div>
        <div id="calendar"></div>
    </div>

        <!--AGREGAR UN NUEVO EVENTO -->
    <!-- Modal Agregar Evento -->
    <div id="modalAgregarEvento" class="modal">
        <div class="modal-contenido">
            <span class="cerrar-modal" onclick="cerrarModal()">&times;</span>
            <h2 class="h2-modal" id="modal-titulo">Agregar Nuevo Evento</h2>
            <form id="formAgregarEvento" action="agregar_evento.php" method="POST">
                <fieldset class="fieldset-modal">
                    <legend class="label-modal" for="descripcion">Título - Detalle:</legend>
                    <textarea id="descripcion" name="descripcion" rows="2" cols="40" required></textarea>
                </fieldset>
                <fieldset class="fieldset-modal">
                    <legend class="label-modal">Inicia</legend>
                    Fecha: <input type="date" id="fecha_ini" name="fecha_ini" required>
                    Hora: <input type="time" name="hora_ini" id="hora_ini">
                </fieldset>
                <fieldset class="fieldset-modal">
                    <legend class="label-modal">Finaliza</legend>
                    Fecha: <input type="date" id="fecha_fin" name="fecha_fin" required>
                    Hora: <input type="time" name="hora_fin" id="hora_fin">
                </fieldset>
                <button class="btn-calendario" type="submit" id="btn-guardar">Guardar Evento</button>
            </form>
        </div>
    </div>

    <!-- Modal Editar Evento -->
    <div id="modalEditarEvento" class="modal">
        <div class="modal-contenido">
            <span id="cerrarModalEditar" class="cerrar-modal">&times;</span>
            <h2 class="h2-modal" id="modal-titulo">Modificar Evento</h2>
            <form id="formEditarEvento" action="editar_evento.php" method="POST">
                <input type="hidden" name="idcalendario" id="idcalendario">
                <fieldset class="fieldset-modal">
                    <legend class="label-modal" for="inputDescripcion">Título - Detalle:</legend>
                    <textarea id="inputDescripcion" name="inputDescripcion" rows="2" cols="40" style="text-transform: capitalize;" required></textarea>
                </fieldset>
                <fieldset class="fieldset-modal">
                    <legend class="label-modal">Inicia</legend>
                    Fecha: <input type="date" id="inputFechaInicio" name="inputFechaInicio" required>
                    Hora: <input type="time" name="inputHoraInicio" id="inputHoraInicio">
                </fieldset>
                <fieldset class="fieldset-modal">
                    <legend class="label-modal">Finaliza</legend>
                    Fecha: <input type="date" id="inputFechaFin" name="inputFechaFin" required>
                    Hora: <input type="time" name="inputHoraFin" id="inputHoraFin">
                </fieldset>
                <button class="btn-calendario" type="submit" onclick="guardarCambiosEvento()">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>
</main>
<script src="../static/js/eventos_calendario.js"></script>
<!--<button class="btn-cancelar" type="button" id="btn-finalizar" onclick="finalizarEvento()">Finalizar Evento</button>-->
    <?php
    require "../footer.php";
    ?>
</body>
</html>
