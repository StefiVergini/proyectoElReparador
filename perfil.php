<?php
session_start();
require './conexionPDO.php';
$error = '';
$success_message = '';
if (!isset($_SESSION["usuario"])) {
    header('Location: login.php'); // Redirijo a la página de login si no hay sesión activa
    exit();
}
$usuario = $_SESSION["usuario"];
//obtener datos del empleado
function obtenerEmpleado($usuario, $base)
{
    try {
        $sql = "SELECT * FROM empleados WHERE email_empleado = :usuario";
        $stmt = $base->prepare($sql);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        throw new Exception("Error al obtener los datos del empleado: " . $e->getMessage());
    }
}
// datos del empleado
$empleado = [];
try {
    $empleado = obtenerEmpleado($usuario, $base);
    if (!$empleado) {
        $error = 'Empleado no encontrado';
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
$email = isset($empleado['email_empleado']) ? $empleado['email_empleado'] : '';
$nombre = isset($empleado['nom_empleado']) ? $empleado['nom_empleado'] : '';
$apellido = isset($empleado['ape_empleado']) ? $empleado['ape_empleado'] : '';
$telefono = isset($empleado['tel_empleado']) ? $empleado['tel_empleado'] : '';
$direccion = isset($empleado['dir_empleado']) ? $empleado['dir_empleado'] : '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'perfil_logica.php';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="./static/styles/perfil.css">
</head>

<body>
    <div class="perfil-container">
        <header>
            <h1>Editar Perfil</h1>
        </header>

        <section class="perfil-contenido">
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <form action="perfil.php" method="POST">
                <!-- email único, no editable -->
                <div class="campo">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" disabled required>
                </div>
                <div class="campo">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                </div>
                <div class="campo">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>
                </div>
                <div class="campo">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required>
                </div>
                <div class="campo">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($direccion); ?>" required>
                </div>
                <div class="campo">
                    <button type="submit" class="btn">Guardar Cambios</button>
                </div>
                <div class="campo">
                    <button type="button" class="btn volver" onclick="window.location.href='electrodomesticos/electrodomesticos.php';">Volver</button>
                </div>
            </form>
        </section>
    </div>
</body>

</html>