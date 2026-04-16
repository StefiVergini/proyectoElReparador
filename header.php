<?php
session_start(); // Inicia la sesión al comienzo del archivo

// Verificar si las variables de sesión están definidas
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id']) || !isset($_SESSION['rol'])) {
    // Redirigir al login si no están definidas
    header('Location: /php/proyectoElReparador/login/index.php');
    exit();
}

$usuario = $_SESSION['usuario'];
$id = $_SESSION['id'];
$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];
$local = $_SESSION['local'];
?>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<header>
  <div class="header">
    <a href="/php/proyectoElReparador/electrodomesticos/inicioElectro.php">
    <img class="logo-header"
      src='../static/images/logo.png'
      alt="logo"
      class="logo" /></a>


    <button class="menu-toggle" onclick="toggleMenu()">
      <i class="fas fa-bars"></i>
    </button>
    <div class="header-derecha">

      <div class="header-container-mensaje">
        <a href="/php/proyectoElReparador/mail/mail.php"><img src="../static/images/mensaje.png" alt="mensajes" class="icono-mensaje" /></a>
        <form action="../cerrar_sesion.php" id="selectCerrarSesion" method="post"></form>
        <form action="../cambiarPass.php" id="selectChange" method="post"></form>
        <form action="../perfil.php" id="selectPerfil" method="post"></form>
        
        <div id="notification-container" style="position: relative; display: inline-block; margin-right: 20px;">
          <i class="fas fa-bell" id="notification-icon" style="font-size: 24px; cursor: pointer; color: #FFFFFF; margin-left:5px; background-color: #79c2f7; padding:6px; border-radius: 50%; border: solid #FFFFFF 3px"></i>
          <span id="notification-badge" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; display: none;">0</span>
        
        <!-- Dropdown para mostrar las notificaciones -->
          <div id="notification-dropdown" style="display: none; position: absolute; top: 30px; right: 0; background: #fff; border: 1px solid #ddd; max-height: 300px; overflow-y: auto; z-index: 1000; width: 300px;">
          <!-- Las notificaciones se cargarán aquí vía AJAX -->
          
          </div>
        </div>

        <!--
        <div class="color">
            <form action="/search_results.html" method="get" class="search-form">
              <input type="search" class="input-buscador" name="query" placeholder="Buscar" required />
              <button type="submit" class="btn-buscar">
                <img src="../static/images/lupa.png" alt="Buscar" />
              </button>
            </form>
        </div>
        -->
        <select class="user" name="" id="select_opciones" onchange="handleSelectChange(this)">
          <option value="nombre">Hola,
            <?php
            if ($usuario) {
              echo ucwords($nombre);
            } else {
              echo "xxx!";
            }
            ?>
          </option>
          <option value="perfil">Mi perfil</option>
          <option value="cpass">Cambiar Contraseña</option>
         
          <option value="cerrar_sesion">Cerrar sesion</option>
        </select>
        
      </div>
      <nav class='nav-container'>
        <ul class="navegacion" id="nav-menu">
           <li class="color">
            <a class="link-navegacion" href="/php/proyectoElReparador/calendario/inicioCalendario.php">
              Calendario</a>
          </li>
           <li class="color">
            <a class="link-navegacion" href="/php/proyectoElReparador/clientes/inicioClientes.php">
              Clientes</a>
          </li>
          <li class="color">
            <a class="link-navegacion" href="/php/proyectoElReparador/electrodomesticos/inicioElectro.php">Electrodomesticos
            </a>
          </li>
           <li class="color "> 
            <div class="desplegable">
                <button class="link-navegacion nav-button">Empleados&#9207;</button>
                  <div class="link">
                      <a href="/php/proyectoElReparador/empleados/inicioEmp.php">Empleados Activos</a>
                      <?php
                        if($rol == 2 || $rol == 4 || $rol == 5 || $rol == 6){
                      ?>
                        
                        <a href="/php/proyectoElReparador/empleados/altaEmp.php">Nuevo Empleado</a>
                        <a href="/php/proyectoElReparador/empleados/cambioCatEmp.php">Cambiar Categoria</a>
                        <a href="/php/proyectoElReparador/empleados/empInactivos.php">Antiguos Empleados</a>
                      <?php
                         }
                      ?>
                      <a href="/php/proyectoElReparador/empleados/historial_x_emp.php">Historial Empleado</a>

                      

                  </div>
            </div>
	        </li>
          <li class="color">
              <div class="desplegable">
                  <button class="link-navegacion nav-button">Reportes&#9207;</button>
                  <div class="link">
                      <a href="/php/proyectoElReparador/reportes/reporte_reparaciones.php">Reparaciones</a>
                      <a href="/php/proyectoElReparador/reportes/reporte_ingresos.php">Ingreso de Dinero</a>
                      <a href="/php/proyectoElReparador/reportes/reporte_tecnicos.php">Estadisticas tecnicos</a>
                  </div>
              </div>   
          </li>
          
          <li class="color">
            <a class="link-navegacion" href="/php/proyectoElReparador/pedidos/pedidosActivos.php">
              Pedidos</a>
          </li>
          <li class="color">
            <a class="link-navegacion" href="/php/proyectoElReparador/proveedores/inicioProv.php">
              Proveedores</a>
          </li>
          <li class="color">
            <a class="link-navegacion" href="/php/proyectoElReparador/stock/inicioStock.php">
              Stock</a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
  
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script src="/php/proyectoElReparador/static/js/notificaciones.js"></script>
  <script>
    function toggleMenu() {
      const menu = document.getElementById("nav-menu");
      menu.classList.toggle("active");
    }
  </script>
  <script>
    function setupMobileDropdowns() {
      const isMobile = window.innerWidth <= 694;

      if (isMobile) {
        // Selecciona todos los botones con submenús
        const buttons = document.querySelectorAll('.desplegable .nav-button');

        buttons.forEach(button => {
          button.addEventListener('click', function (e) {
            e.preventDefault(); // Previene que el link se dispare

            const submenu = this.nextElementSibling;

            // Alternar visibilidad
            if (submenu.style.display === 'block') {
              submenu.style.display = 'none';
            } else {
              // Ocultar otros desplegables abiertos
              document.querySelectorAll('.desplegable .link').forEach(el => {
                el.style.display = 'none';
              });
              submenu.style.display = 'block';
            }
          });
        });
      }
    }

    document.addEventListener('DOMContentLoaded', setupMobileDropdowns);
    window.addEventListener('resize', () => {
      // Reiniciar estado si el tamaño cambia
      document.querySelectorAll('.desplegable .link').forEach(el => el.style.display = '');
      setupMobileDropdowns();
    });
  </script>

</header>

