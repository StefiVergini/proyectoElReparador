let id_evento_global;

document.addEventListener('DOMContentLoaded', function() {
    const modalAgregarEvento = document.getElementById("modalAgregarEvento");
    const modalEditarEvento = document.getElementById("modalEditarEvento");
    const cerrarModalAgregar = document.querySelector(".cerrar-modal");
    const cerrarModalEditar = document.getElementById('cerrarModalEditar');

    // Inicializar FullCalendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetchEventsFromDatabase(successCallback, failureCallback);
        },
        dateClick: function(info) {
            // Llenar los campos de fecha y hora en el formulario del modal
            document.getElementById('fecha_ini').value = info.dateStr; // Solo la fecha
            document.getElementById('hora_ini').value = info.dateStr.includes("T") ? info.dateStr.split("T")[1].slice(0, 5) : ''; // Extrae hora en formato 'HH:MM' si existe
            abrirModalAgregar(); // Abre el modal para agregar el evento
        },
        eventClick: function(info) {
            const evento = info.event; // info.event es el evento de FullCalendar que se clickeó
            abrirModalEditar(
                evento.id, // ID del evento 
                evento.title, // Descripción del evento
                evento.start.toISOString().split('T')[0], // Fecha de inicio
                evento.start.toTimeString().split(' ')[0], // Hora de inicio
                evento.end ? evento.end.toISOString().split('T')[0] : null, // Fecha de fin (si existe)
                evento.end ? evento.end.toTimeString().split(' ')[0] : null, // Hora de fin (si existe)
                //modalEditarEvento.style.display = "block" 
            );
        }
    });
    calendar.render();

    // Evento para abrir el modal al hacer clic en el botón "+"
    document.querySelector('.agregar-evento-btn').addEventListener('click', function() {
        document.getElementById('fecha_ini').value = ''; // Limpiar campos
        document.getElementById('hora_ini').value = '';
        abrirModalAgregar(); // Abre el modal para agregar el evento
    });

    // Función para cargar los eventos en la lista
    fetch('leer_eventos.php')
    .then(response => {
        if (!response.ok) { // Comprobación del estado de la respuesta
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        //console.log(data);
        const eventListContainer = document.getElementById('event-list-container');
        eventListContainer.innerHTML = '';
        data.forEach(evento => {
            // Crear un objeto Date para las fechas
            const hoy = new Date();
            
            console.log(hoy.toLocaleDateString());
            const fechaInicio = new Date(evento.fecha_inicio);
            const fechaFin = new Date(evento.fecha_fin);
    
            // Sumar un día a cada fecha - aparecen mal en js es un error comun
            fechaInicio.setDate(fechaInicio.getDate() + 1);
            fechaFin.setDate(fechaFin.getDate() + 1);
    
            const eventItem = document.createElement('div');
            eventItem.className = `evento-item evento-tipo-${evento.tipo}`;
            eventItem.style.backgroundColor = evento.color;
            if(fechaFin >= hoy){
                // Construcción del HTML de cada evento
                let eventHTML = `
                    <span class="titulo-evento" style="font-weight: bold; font-size: 15px; text-transform: capitalize;">
                        ${evento.descripcion_evento.trim()} - 
                        Inicio: ${fechaInicio.toLocaleDateString()} - 
                        Fin: ${fechaFin.toLocaleDateString()}
                    </span>`;
                const estadoEvento = Number(evento.estado_evento);
                // Botón de editar
                if (estadoEvento === 1) { // Evento activo
                    eventHTML += `
                        <span class="icono-eventos editar-evento" data-id="${evento.idcalendario}" 
                            data-descripcion="${evento.descripcion_evento}" 
                            data-fecha-inicio="${evento.fecha_inicio}" 
                            data-hora-inicio="${evento.hora_inicio}" 
                            data-fecha-fin="${evento.fecha_fin}" 
                            data-hora-fin="${evento.hora_fin}">
                            <img class= "img" src="../static/images/editar.png" alt="Editar" width='20' height='20'>
                        </span>`;
                    
                    // Botón de eliminar
                    eventHTML += `
                        <span class="icono-eventos eliminar-evento" data-id="${evento.idcalendario}">
                            <img class= "img" src="../static/images/borrar.png" alt="Eliminar" width='20' height='20'>
                        </span>`;
                } else {
                    
                    // Botón de reactivar (actualizar)
                    eventHTML += `
                        <span class="icono-eventos actualizar-evento" data-id="${evento.idcalendario}">
                            <img class= "img" src="../static/images/actualizar.png" alt="Reactivar" width='20' height='20'>
                        </span>`;
                }
        
                // Insertar el HTML en el contenedor del evento
                eventItem.innerHTML = eventHTML;
                eventListContainer.appendChild(eventItem);
            }

        });
    
        // Agregar listeners para los botones de editar y eliminar/actualizar después de renderizar los eventos
        document.querySelectorAll('.editar-evento').forEach(span => {
            if (!span.classList.contains('disabled')) { // Solo agregar listener si no está deshabilitado
                span.addEventListener('click', function() {
                    const idEvento = this.getAttribute('data-id');
                    const descripcion = this.getAttribute('data-descripcion');
                    const fechaInicio = this.getAttribute('data-fecha-inicio');
                    const horaInicio = this.getAttribute('data-hora-inicio');
                    const fechaFin = this.getAttribute('data-fecha-fin');
                    const horaFin = this.getAttribute('data-hora-fin');
                    
                    abrirModalEditar(idEvento, descripcion, fechaInicio, horaInicio, fechaFin, horaFin);
                });
            }
        });
    
        document.querySelectorAll('.eliminar-evento').forEach(span => {
            span.addEventListener('click', function() {
                const idEvento = this.getAttribute('data-id');
                eliminarEvento(idEvento);
            });
        });
    
        document.querySelectorAll('.actualizar-evento').forEach(span => {
            span.addEventListener('click', function() {
                const idEvento = this.getAttribute('data-id');
                reactivarEvento(idEvento);
            });
        });
    })
    .catch(error => console.error('Error al cargar eventos:', error));
    // Cerrar modal de editar evento
    cerrarModalEditar.onclick = function() {
        modalEditarEvento.style.display = "none";
        modalEditarEvento.style.zIndex = ""; // Restablecer el z-index original
    };
    // Cerrar el modal de agregar
    cerrarModalAgregar.onclick = function() {
        modalAgregarEvento.style.display = "none";
    }
    // Cerrar el modal al hacer clic fuera de él
    window.onclick = function(event) {
        if (event.target == modalAgregarEvento) {
            modalAgregarEvento.style.display = "none";
        }
        if (event.target == modalEditarEvento) {
            modalEditarEvento.style.display = "none";
        }
    }


});

// Función para abrir el modal de agregar evento
function abrirModalAgregar() {
    const modal = document.getElementById("modalAgregarEvento");
    modal.style.display = "block"; // Muestra el modal
}

function abrirModalEditar(idEvento, descripcion, fechaInicio, horaInicio, fechaFin, horaFin) {
    const modalEditar = document.getElementById("modalEditarEvento");
    document.getElementById('idcalendario').value = idEvento; 
    //console.log("HOLAAAAAAA");
    if (modalEditar) {
        modalEditar.style.display = "block"; // Asegúrate de que esté aquí
        //console.log("Modal de edición abierto con ID:", idEvento); // Para verificar que esta línea se ejecuta
        // Aumentar el z-index del modal
        modalEditar.style.zIndex = "9999";
        // Asegura de que idEvento sea numérico
        const numericId = parseInt(idEvento, 10);
        // Llenar campos dentro del modal
        document.getElementById('inputDescripcion').value = descripcion;
        document.getElementById('inputFechaInicio').value = fechaInicio;
        document.getElementById('inputHoraInicio').value = horaInicio;
        document.getElementById('inputFechaFin').value = fechaFin;
        document.getElementById('inputHoraFin').value = horaFin;
        window.id_evento_global = numericId;
    } else {
        console.error("No se encontró el modal de edición.");
    }

}

function guardarCambiosEvento() {
    const idEvento = parseInt(document.getElementById('idcalendario').value, 10);
    const descripcion = document.getElementById('inputDescripcion').value;
    const fechaInicio = document.getElementById('inputFechaInicio').value;
    const horaInicio = document.getElementById('inputHoraInicio').value;
    const fechaFin = document.getElementById('inputFechaFin').value;
    const horaFin = document.getElementById('inputHoraFin').value;

    fetch('editar_evento.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json' // Mantener como JSON
        },
        body: JSON.stringify({
            idcalendario: idEvento, // cambiar a idcalendario
            inputDescripcion: descripcion,
            inputFechaInicio: fechaInicio,
            inputHoraInicio: horaInicio,
            inputFechaFin: fechaFin,
            inputHoraFin: horaFin
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Response Data:", data);

    })
    .catch(error => console.error('Error al actualizar evento:', error));
}
// Función para finalizar un evento
function reactivarEvento(idEvento) {
    if (confirm('¿Estás seguro de que deseas reactivar este evento?')) {
        // Realiza una petición a tu archivo PHP para eliminar el evento
        fetch('actualizar_evento.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id_evento: idEvento })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Evento reactivado con éxito');
                location.reload(); // Recargar la página para ver los cambios
            } else {
                alert('Error al reactivar el evento');
            }
        })
        .catch(error => console.error('Error al eliminar evento:', error));
    }
}

// Función para eliminar un evento
function eliminarEvento(idEvento) {
    if (confirm('¿Estás seguro de que deseas finalizar este evento?')) {
        // Realiza una petición a tu archivo PHP para eliminar el evento
        fetch('eliminar_evento.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id_evento: idEvento })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Evento eliminado con éxito');
                location.reload(); // Recargar la página para ver los cambios
            } else {
                alert('Error al eliminar el evento');
            }
        })
        .catch(error => console.error('Error al eliminar evento:', error));
    }
}

// Función para traer los eventos de la base de datos y formatearlos para FullCalendar
function fetchEventsFromDatabase(successCallback, failureCallback) {
    fetch('leer_eventos.php')
        .then(response => response.json())
        .then(data => {
            const eventos = data.map(evento => ({
                id: evento.idcalendario,
                title: evento.descripcion_evento,
                start: `${evento.fecha_inicio}T${evento.hora_inicio}`,
                end: `${evento.fecha_fin}T${evento.hora_fin}`,
                backgroundColor: evento.color || '#3a87ad',
                borderColor: evento.color || '#3a87ad',
                allDay: false
            }));

            successCallback(eventos);
        })
        .catch(error => {
            console.error("Error al cargar eventos:", error);
            failureCallback(error);
        });
}


