<?php
session_start(); // Inicia la sesión al comienzo del archivo

// Verificar si las variables de sesión están definidas
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id']) || !isset($_SESSION['rol'])) {
    // Redirigir al login si no están definidas
    header('Location: /php/proyectoElReparador/login/index.php');
    exit();
}

// Si las variables de sesión están definidas, puedes usarlas aquí
$usuario = $_SESSION['usuario'];
$id = $_SESSION['id'];
$rol = $_SESSION['rol'];
?>
<header>
  <div class="header">
    <img class="logo-header"
      src='../static/images/logo.png'
      alt="logo"
      class="logo" />
    <div class="header-derecha">
      <div class="header-container-mensaje">
        <a href="/php/proyectoElReparador/mail/mail.php"><img src="../static/images/mensaje.png" alt="mensajes" class="icono-mensaje" /></a>
        <form action="../cerrar_sesion.php" id="selectCerrarSesion" method="post"></form>
        <form action="../cambiarPass.php" id="selectChange" method="post"></form>
        <form action="../perfil.php" id="selectPerfil" method="post"></form>

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
              echo "$usuario";
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
        <ul class="navegacion">
          <li class="color">
            <a class="link-navegacion" href="/php/proyectoElReparador/electrodomesticos/electrodomesticos.php">Electrodomesticos
            </a>
          </li>
          <li class="color">
            <a class="link-navegacion" href="/php/proyectoElReparador/stock/stock_Consultas.php">
              Stock</a>
          </li>
          <li class="color">
            <a class="link-navegacion" href="/php/proyectoElReparador/pedidos/pedidos_consulta.php">
              Pedidos</a>
          </li>
          <li class="color">
            <a class="link-navegacion" href="/php/proyectoElReparador/proveedores/inicioProv.php">
              Proveedores</a>
          </li>
          <li class="color">
            <a class="link-navegacion" href="/php/proyectoElReparador/clientes/leer_db.php">
              Clientes</a>
          </li>
          <li class="color">
            <a class="link-navegacion" href="/php/proyectoElReparador/calendario/inicioCalendario.php">
              Calendario</a>
          </li>
          <li class="color ">
            <form method="POST" action="">
              <select class="link-navegacion select-custom emp-link" name="opEmp" id="opEmp" onchange="this.form.submit()" >
                <option value="">- Empleados -</option>
                <option value="inicioEmp">Empleados Activos</option>
                <option value="histEmp">Historial Empleado</option>
                <?php
                if($rol == 2 || $rol == 4 || $rol == 5 || $rol == 6){
                ?>
                  <option value="cambioCat">Cambiar Categoría </option>
                <?php
                }
                ?>
                <option value="empInactivos">Antiguos Empleados</option>
                <?php
                if($rol == 2 || $rol == 4 || $rol == 5 || $rol == 6){
                ?>
                  <option value="altaEmp">Nuevo Empleado</option>
                <?php
                }
                ?>
              </select>
            </form>
	        </li>
        </ul>
      </nav>
    </div>
  </div>
</header>
<?php
//Select empleados, distintas opciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['opEmp'])) {
        switch ($_POST['opEmp']) {
            case 'inicioEmp':
              header("Location: /php/proyectoElReparador/empleados/inicioEmp.php");
              exit();
            case 'altaEmp':
                header("Location: /php/proyectoElReparador/empleados/altaEmp.php");
                exit();
            case 'histEmp':
                header("Location: /php/proyectoElReparador/empleados/historial_x_emp.php");
                exit();
            case 'cambioCat':
                  header("Location: /php/proyectoElReparador/empleados/cambioCatEmp.php");
                  exit();
            case 'empInactivos':
                header("Location: /php/proyectoElReparador/empleados/empInactivos.php");
                exit();
        }
    }
}
?>
