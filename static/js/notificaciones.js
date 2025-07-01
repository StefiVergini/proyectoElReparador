function actualizarNotificaciones() {
  $.ajax({
    url: '/php/proyectoElReparador/notificaciones/buscar_notificaciones.php',
    method: 'GET',
    dataType: 'json',
    success: function(data) {
      // Actualiza el badge (unicamente las no leídas)
      if (data.count > 0) {
        $('#notification-badge').text(data.count).show();
      } else {
        $('#notification-badge').hide();
      }

      // Construir contenido para las notificaciones no leídas
      let contenidoUnread = "<h4 style='margin:10px 0;'>Nuevas</h4>";
      //contenidoUnread += `<button class="mark-read" data-id="${noti.id_notificacion}" style="position: absolute; top: 10px; right: 10px; background: transparent; border: none; color: #007BFF; cursor: pointer;">Marcar leído</button>`;
      data.unread.forEach(function(noti) {
        contenidoUnread += `<div class="notification-item" style="padding: 10px; border-bottom: 1px solid #eee; position: relative;">
                              <a href="${noti.link}" style="color: #333; text-decoration: none;">${noti.mensaje}</a>
                              
                            </div>`;
      });
      if (contenidoUnread === "") {
        contenidoUnread = "<div style='padding: 10px;'>No hay notificaciones nuevas.</div>";
      } else {
        // Agregar un botón global para marcar todas como leídas
        contenidoUnread += `<div style="text-align: center; margin-top: 10px;">
                              <button id="mark-all-read" style="padding: 5px 10px; cursor:pointer; background: transparent; border: none; color: #007BFF;">Marcar todas como leídas</button>
                            </div>`;
      }
      
      // Construir contenido para las notificaciones leídas (histórico)
      let contenidoRead = "<h4 style='margin:10px 0;'>Leídas</h4>";
      data.read.forEach(function(noti) {
        contenidoRead += `<div class="notification-item" style="padding: 10px; border-bottom: 1px solid #eee;">
                              <a href="${noti.link}" style="color: #777; text-decoration: none;">${noti.mensaje}</a>
                           </div>`;
      });
      if (data.read.length === 0) {
        contenidoRead += "<div style='padding: 10px; color:#777;'>No hay notificaciones leídas recientemente.</div>";
      }
      
      // Concatenar ambas secciones en el dropdown
      let contenidoTotal = "<div>" + contenidoUnread + "</div><hr>" + "<div>" + contenidoRead + "</div>";
      $('#notification-dropdown').html(contenidoTotal);
    },
    error: function(err) {
      console.error("Error al cargar las notificaciones:", err);
    }
  });
}

// Actualización y configuraciones
setInterval(actualizarNotificaciones, 30000);
$(document).ready(function(){
  actualizarNotificaciones();
  
  // Toggle dropdown al hacer clic en el ícono
  $('#notification-icon').on('click', function(e){
    e.stopPropagation();
    $('#notification-dropdown').toggle();
  });
  
  // Cerrar el dropdown si se hace clic fuera
  $(document).click(function(){
    $('#notification-dropdown').hide();
  });
  
  $(document).on('click', '#mark-all-read', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $.ajax({
      url: '/php/proyectoElReparador/notificaciones/marcar_notificacion.php',
      method: 'POST',
      success: function(response) {
        console.log("Todas las notificaciones marcadas como leídas:", response);
        actualizarNotificaciones();
      },
      error: function(err) {
        console.error("Error al marcar todas las notificaciones:", err);
      }
    });
  });
});
