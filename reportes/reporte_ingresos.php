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
    <link rel="stylesheet" href="../static/styles/reportes.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
    <head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body>
    <h1  class="titulo">Reportes de Ingreso de Dinero</h1>
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

<form method="POST" action="" class="form-reporte" id="formFiltro">
    <label class="label" for="rango">Selecciona un rango de fechas:</label>
    <input class="input-reportes" type="text" id="rango" name="rango" placeholder="YYYY-MM-DD to YYYY-MM-DD" required
       value="<?php echo isset($_POST['rango']) ? htmlspecialchars($_POST['rango']) : ''; ?>">

    <div class="formulario-contenedor">
        <label class="label" for="medio_pago">Medio de pago
            <select class="input-reportes" name="medio_pago" id="medio_pago">
                <option value="">Todos</option>
                <option value="Efectivo" <?php echo (isset($_POST['medio_pago']) && $_POST['medio_pago'] == 'Efectivo') ? 'selected' : ''; ?>>Efectivo</option>
                <option value="Transferencia" <?php echo (isset($_POST['medio_pago']) && $_POST['medio_pago'] == 'Transferencia') ? 'selected' : ''; ?>>Transferencia</option>

            </select>
        </label>
    </div>

    <div class="formulario-contenedor">
        <label class="label" for="tipo_ingreso">Tipo de ingreso
            <select class="input-reportes" name="tipo_ingreso" id="tipo_ingreso">
                <option value="">Todos</option>
                <option value="fijo" <?php echo (isset($_POST['tipo_ingreso']) && $_POST['tipo_ingreso'] == 'fijo') ? 'selected' : ''; ?>>Monto Fijo</option>
                <option value="saldo" <?php echo (isset($_POST['tipo_ingreso']) && $_POST['tipo_ingreso'] == 'saldo') ? 'selected' : ''; ?>>Monto Reparación</option>

            </select>
        </label>
    </div>

    <div class="formulario-contenedor">
        <label class="label" for="tipo">Tipo de Electrodoméstico
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
        </label>
    </div>
     
    <div style="margin-top: 30px;">
        <button type="submit">Filtrar</button>
        <button type="button" id="limpiar">Limpiar</button>
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
    echo  "<div style='text-align: center; margin-top:40px;'>
            <strong style='color: black;'>Mostrando resultados desde el $fecha_inicio hasta el $fecha_fin</strong>
          </div>";
    
    echo "<div style='text-align: center; margin-top:40px; margin-bottom: 5px;'>
            <p><strong style='color: blue'>Total ingresos por monto fijo:</strong> $" . number_format($total_fijo, 2, ',', '.') . "</p>
            <p><strong style='color: blue'>Total ingresos por Reparaciones:</strong> $" . number_format($total_saldo, 2, ',', '.') . "</p>
          </div>";

    ?>
        <div class="div-con-botones">
            <div class="desplegable">
                <button class="btn-reportes">Exportar &#9207;</button>
                    <div class="link">
                        <a target="_blank" href="pdf_ingresos.php?<?php echo $queryString; ?>">PDF</a>
                        <a class="excel" href="excel_ingresos.php?<?php echo $queryString; ?>">Excel</a>
                    </div>
            </div>
        </div>
    <?php
    echo "<div class='table-container'>";
    echo "<table class='tabla'>";

            echo "<thead>
                    <tr>
                        <th class='tabla-head'>ID</th>
                        <th class='tabla-head'>Cliente</th>
                        <th class='tabla-head'>Electrodomestico</th>
                        <th class='tabla-head'>Fecha Cobro inicial</th>
                        <th class='tabla-head'>Medio Pago Inicial</th>
                        <th class='tabla-head'>Fecha cobro Final</th>
                        <th class='tabla-head'>Medio Pago Final</th>
                        <th class='tabla-head'>Monto Fijo</th>
                        <th class='tabla-head'>Saldo</th>
                    </tr>
                  </thead><tbody>";
            foreach ($resultados as $fila) {
                echo "<tr>";
                echo "<td class='tabla-data'>{$fila['id_cobro']}</td>";
                echo "<td class='tabla-data'>{$fila['nom_cliente']} {$fila['ape_cliente']}</td>";
                echo "<td class='tabla-data'>{$fila['tipo_electro_nombre']}</td>";
                echo "<td class='tabla-data'>{$fila['fecha_cobro_inicial']}</td>";
                echo "<td class='tabla-data'>{$fila['medio_pago_inicial']}</td>";
                echo "<td class='tabla-data'>{$fila['fecha_cobro_final']}</td>";
                echo "<td class='tabla-data'>{$fila['medio_pago_final']}</td>";
                echo "<td class='tabla-data'>{$fila['arancel_fijo_cobrado']}</td>";
                echo "<td class='tabla-data'>{$fila['monto_final_repa']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";

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
        document.getElementById("limpiar").addEventListener("click", function() {
                document.getElementById("rango").value = "";
                document.getElementById("medio_pago").value = "";
                document.getElementById("tipo_ingreso").value = "";
                document.getElementById("tipo").value = "";
                document.getElementById("formFiltro").submit(); // Envía el formulario para limpiar resultados
            });
    </script>
     
</body>
</html>