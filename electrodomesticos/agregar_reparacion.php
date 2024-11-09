<?php
require '../conexionPDO.php';
include("../header.php");

$clientes = [];
$error = '';
$cliente_no_encontrado = false;
$success_message = '';

$idempleados = $id ?? '';
$tipos_electro = [];
try {
    $sql_electro = "SELECT * FROM tipo_electro";
    $stmt_electro = $base->prepare($sql_electro);
    $stmt_electro->execute(); 
    $tipos_electro = $stmt_electro->fetchAll(PDO::FETCH_ASSOC);
    $no_electrodomesticos = empty($tipos_electro);
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['cliente_id'])) {
    $identificacion = $_POST['identificacion'] ?? '';
    $tipo_busqueda = $_POST['tipo'] ?? '';
    try {
        $sql_cliente = ($tipo_busqueda == 'dni')
            ? "SELECT * FROM clientes WHERE dni_cliente = :identificacion"
            : "SELECT * FROM clientes WHERE idclientes = :identificacion";

        $stmt_cliente = $base->prepare($sql_cliente);
        $stmt_cliente->execute(['identificacion' => $identificacion]);
        $clientes = $stmt_cliente->fetchAll(PDO::FETCH_ASSOC);
        if (empty($clientes)) {
            $cliente_no_encontrado = true;
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cliente_id'])) {
    include 'agregar_reparacion_logica.php';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Agregar Reparación</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/electro/electro.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <main>
        <div class="contenedor_agregar">
            <h1>Agregar Nueva Reparación</h1>
        
            <div class="formulario-consulta">
                <form action="agregar_reparacion.php" method="post">
                    <div class="form-add">
                        <h4>Buscar Cliente - Seleccione opcion</h4>
                        <table class="tabla-consulta">
                            <tr>
                                <td class="label">
                                    <input type="radio" id="dni" name="tipo" value="dni" required />
                                    <label for="dni">DNI</label>
                                </td>
                                <td class="label">
                                    <input type="radio" id="id" name="tipo" value="id" required />
                                    <label for="id">ID</label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="input-identificacion">
                                        <input type="text" id="identificacion" name="identificacion"
                                            placeholder="Ingrese DNI/ID Cliente" required />
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="botones">
                            <button type="submit" class="btn-e">Buscar</button>
                            <button type="button" onclick="window.location.href='electrodomesticos.php';"
                                class="btn-e">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php if ($error): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($cliente_no_encontrado): ?>
            <p style="color:red; text-align: center; font-weight: bold; font-size: 26px; margin-top: 20px;">El cliente no se encuentra en la base de datos. <a
                    href="../clientes/clientes_agregar.php">Agregue el cliente aquí.</a></p>
        <?php endif; ?>
        <?php if (!empty($clientes)): ?>
            <form action="agregar_reparacion.php" method="post">
                <h5>Datos del Cliente:</h5>
                <?php foreach ($clientes as $cliente): ?>
                    <input type="hidden" name="cliente_id" value="<?php echo $cliente['idclientes']; ?>" />
                    <div class="tarjeta-datos-cliente">
                        <input type="text" name="nombre_cliente"
                            value="<?php echo htmlspecialchars($cliente['nom_cliente']); ?>" required disabled />
                        <input type="text" name="apellido_cliente"
                            value="<?php echo htmlspecialchars($cliente['ape_cliente']); ?>" required disabled />
                        <input type="text" name="tel_cliente" value="<?php echo htmlspecialchars($cliente['tel_cliente']); ?>"required disabled />
                        <input type="email" name="email_cliente"
                            value="<?php echo htmlspecialchars($cliente['email_cliente']); ?>" required disabled />
                        <input type="text" name="direccion_cliente"
                            value="<?php echo htmlspecialchars($cliente['dir_cliente']); ?>" required disabled />
                        </>
                    </div>
                <?php endforeach; ?>
                </div>
                <div>
                    <h5>Datos del Electrodoméstico:</h5>
                    <div class="tarjeta-datos-cliente">
                        <select name="idtipo_electro" required>
                            <option value="" disabled selected>Tipo de electrodoméstico</option>
                            <?php foreach ($tipos_electro as $tipo): ?>
                                <option value="<?php echo htmlspecialchars($tipo['idtipo_electro']); ?>">
                                    <?php echo htmlspecialchars($tipo['nom_tipo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="marca" maxlength="20" placeholder="Marca Electro." required />
                        <input type="text" name="modelo" maxlength="20" placeholder="Modelo Electro." required />
                        <input type="text" name="nro_serie" maxlength="20" placeholder="Nro. Serie" required />
                        <input type="text" name="color" maxlength="10" placeholder="Color" />
                        <textarea name="descripcion" maxlength="200" required
                            placeholder="Descripcion de la reparación"></textarea>
                    </div>
                </div>
                <input type="hidden" name="idempleados" value="<?php echo htmlspecialchars($idempleados); ?>" />
                <div class="cont-btn">
                    <button type="submit" class="btn-agregar">Agregar Reparación</button>
                </div>
            </form>
        <?php endif; ?>
        </div>
        
    </main>
    <?php
    require '../footer.php';
    ?>
</body>

</html>