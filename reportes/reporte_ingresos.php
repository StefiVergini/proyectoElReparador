<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("../electrodomesticos/electro_class.php");
    include("../empleados/empleados_class.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/tablas.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
    <head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body>
    <h1  class="titulo">Generar reporte</h1>
    <?php
// Construir cadena de query para exportación
$params = [
    'rango' => $_POST['rango'] ?? '',
    'medio_pago' => $_POST['medio_pago'] ?? '',
    'tipo_ingreso' => $_POST['tipo_ingreso'] ?? '',
    'tipo' => $_POST['tipo'] ?? ''
];
$queryString = http_build_query($params);
?>

<form method="POST" action="">
    <label style="padding-left: 20;" class="label" for="rango">Selecciona un rango de fechas:</label>
    <input class="input-reportes" type="text" id="rango" name="rango" placeholder="YYYY-MM-DD to YYYY-MM-DD" required
       value="<?php echo isset($_POST['rango']) ? htmlspecialchars($_POST['rango']) : ''; ?>">

    <div class="form-group">
        <label style="padding-left: 20;" class="label" for="medio_pago">Medio de pago</label>
        <select class="input-reportes" name="medio_pago" id="medio_pago">
            <option value="">Todos</option>
            <option value="Efectivo" <?php echo (isset($_POST['medio_pago']) && $_POST['medio_pago'] == 'Efectivo') ? 'selected' : ''; ?>>Efectivo</option>
            <option value="Transferencia" <?php echo (isset($_POST['medio_pago']) && $_POST['medio_pago'] == 'Transferencia') ? 'selected' : ''; ?>>Transferencia</option>

        </select>
    </div>

    <div class="form-group">
        <label style="padding-left: 20;" class="label" for="tipo_ingreso">Tipo de ingreso</label>
        <select class="input-reportes" name="tipo_ingreso" id="tipo_ingreso">
            <option value="">Todos</option>
            <option value="fijo" <?php echo (isset($_POST['tipo_ingreso']) && $_POST['tipo_ingreso'] == 'fijo') ? 'selected' : ''; ?>>Monto fijo</option>
            <option value="saldo" <?php echo (isset($_POST['tipo_ingreso']) && $_POST['tipo_ingreso'] == 'saldo') ? 'selected' : ''; ?>>Saldo</option>

        </select>
    </div>

    <div class="form-group">
        <label style="padding-left: 20;" class="label" for="tipo">Tipo de Electrodoméstico</label>
        <select class="input-reportes" name="tipo" id="tipo">
        <option value="">Todos</option>
    <?php 
        $electros = new Electro($base);
        $tipos = $electros->leerTipoElectro();
        foreach ($tipos as $tipoObj) {
            $tipoId = $tipoObj->getTipoElectro();
            $nomTipo = $tipoObj->getNomTipo();
            $selected = ($tipoId == ($_GET['tipo'] ?? '')) ? 'selected' : '';
            echo "<option value=\"$tipoId\" $selected>" . ucwords($nomTipo) . "</option>";
        }
    ?>
        </select>
    </div>
    </div>  
        <div style="display: flex; align-items: flex-start; gap: 1rem; margin: 30px 20px;">
            <button class="btn-reportes" type="submit">Filtrar</button>
    
        <div class="desplegable">
            <button class="btn-reportes">Exportar &#9207;</button>
                <div class="link">
                    <a target="_blank" href="pdf_ingresos.php?<?php echo $queryString; ?>">PDF</a>
                    <a class="excel" href="excel_ingresos.php?<?php echo $queryString; ?>">Excel</a>
                </div>
        </div>
    </div>
</form>

<?php
// Obtener tipos de electrodomésticos desde la base de datos
$stmtTipos = $base->prepare("SELECT idtipo_electro, nom_tipo FROM tipo_electro");
$stmtTipos->execute();
$tipos = $stmtTipos->fetchAll(PDO::FETCH_ASSOC);
?>

    <?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["rango"])) {
    $fechas = explode(" to ", $_POST["rango"]);
    if (count($fechas) == 2) {
        $fecha_inicio = trim($fechas[0]);
        $fecha_fin = trim($fechas[1]);

        $medio_pago = $_POST["medio_pago"] ?? "";
        $tipo_ingreso = $_POST["tipo_ingreso"] ?? "";
        $tipo_electro = $_POST["tipo"] ?? "";

        $query = "
        SELECT 
            cobros.*, 
            clientes.nom_cliente, 
            clientes.ape_cliente, 
            tipo_electro.nom_tipo AS tipo_electro_nombre
        FROM cobros
        JOIN reparaciones ON cobros.id_reparacion = reparaciones.id_reparacion
        JOIN electrodomesticos ON reparaciones.idelectrodomesticos = electrodomesticos.idelectrodomesticos
        JOIN clientes ON electrodomesticos.idclientes = clientes.idclientes
        JOIN tipo_electro ON electrodomesticos.tipo_electro = tipo_electro.idtipo_electro
        WHERE (
            (cobros.fecha_cobro_inicial BETWEEN :inicio AND :fin) 
            OR 
            (cobros.fecha_cobro_final BETWEEN :inicio AND :fin)
        )
        ";

        if (!empty($medio_pago)) {
            $query .= " AND (cobros.medio_pago_inicial = :medio_pago OR cobros.medio_pago_final = :medio_pago)";
        }

        if (!empty($tipo_ingreso)) {
            if ($tipo_ingreso === "fijo") {
                $query .= " AND cobros.arancel_fijo_cobrado > 0";
            } elseif ($tipo_ingreso === "saldo") {
                $query .= " AND cobros.monto_final_repa > 0";
            }
        }

        if (!empty($tipo_electro)) {
            $query .= " AND tipo_electro.idtipo_electro = :tipo_electro";
        }

        $stmt = $base->prepare($query);
        $stmt->bindParam(':inicio', $fecha_inicio);
        $stmt->bindParam(':fin', $fecha_fin);

        if (!empty($medio_pago)) {
            $stmt->bindParam(':medio_pago', $medio_pago);
        }

        if (!empty($tipo_electro)) {
            $stmt->bindParam(':tipo_electro', $tipo_electro);
        }

        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_fijo = 0;
        $total_saldo = 0;

        foreach ($resultados as $fila) {
            $total_fijo += floatval($fila['arancel_fijo_cobrado']);
            $total_saldo += floatval($fila['monto_final_repa']);
        }

if ($resultados) {
    echo "<div style='text-align: center; margin: -55px 0 15px 0;'>
            <strong style='color: black;'>Mostrando resultados desde el $fecha_inicio hasta el $fecha_fin</strong>
          </div>";

    echo "<div style='text-align: center; margin-bottom: 25px;'>
            <p><strong style='color: blue'>Total ingreso por monto fijo:</strong> $" . number_format($total_fijo, 2, ',', '.') . "</p>
            <p><strong style='color: blue'>Total ingreso por saldo:</strong> $" . number_format($total_saldo, 2, ',', '.') . "</p>
          </div>";

    echo "<table class='tabla'>";

            echo "<thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Electrodomestico</th>
                        <th>Fecha Cobro inicial</th>
                        <th>Medio Pago Inicial</th>
                        <th>Fecha cobro Final</th>
                        <th>Medio Pago Final</th>
                        <th>Monto Fijo</th>
                        <th>Saldo</th>
                    </tr>
                  </thead><tbody>";
            foreach ($resultados as $fila) {
                echo "<tr>";
                echo "<td>{$fila['id_cobro']}</td>";
                echo "<td>{$fila['nom_cliente']} {$fila['ape_cliente']}</td>";
                echo "<td>{$fila['tipo_electro_nombre']}</td>";
                echo "<td>{$fila['fecha_cobro_inicial']}</td>";
                echo "<td>{$fila['medio_pago_inicial']}</td>";
                echo "<td>{$fila['fecha_cobro_final']}</td>";
                echo "<td>{$fila['medio_pago_final']}</td>";
                echo "<td>{$fila['arancel_fijo_cobrado']}</td>";
                echo "<td>{$fila['monto_final_repa']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";

        } else {
            echo "<p>No se encontraron resultados.</p>";
        }

    } else {
        echo "<p style='color:red;'>Formato de fecha no válido.</p>";
    }
}
?>
    <?php require '../footer.php'; ?>
    <script>
        flatpickr("#rango", {
            mode: "range",
            dateFormat: "Y-m-d"
        });
    </script>  
     
</body>
</html>