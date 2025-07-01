<?php
include("../conexionPDO.php");

if (empty($_GET['rango'])) {
    die("Debe seleccionar un rango de fechas para generar el reporte.");
}

// Forzar descarga .xls
echo "\xEF\xBB\xBF"; // BOM para UTF-8
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=reporte_ingresos.xls");

// Capturar parámetros
$rango = $_GET['rango'];
$medio_pago = $_GET['medio_pago'] ?? '';
$tipo_ingreso = $_GET['tipo_ingreso'] ?? '';
$tipo_electro = $_GET['tipo'] ?? '';

// Parsear rango de fechas
$fechas = explode(" to ", $rango);
if (count($fechas) != 2) {
    die("Rango de fechas no válido.");
}
$inicio = trim($fechas[0]);
$fin = trim($fechas[1]);

// Construir consulta
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
$stmt->bindParam(':inicio', $inicio);
$stmt->bindParam(':fin', $fin);
if (!empty($medio_pago)) {
    $stmt->bindParam(':medio_pago', $medio_pago);
}
if (!empty($tipo_electro)) {
    $stmt->bindParam(':tipo_electro', $tipo_electro);
}
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generar tabla HTML para Excel
echo "<table border='1'>";
echo "<tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Electrodoméstico</th>
        <th>Fecha Cobro Inicial</th>
        <th>Medio Pago Inicial</th>
        <th>Fecha Cobro Final</th>
        <th>Medio Pago Final</th>
        <th>Monto Fijo</th>
        <th>Saldo</th>
      </tr>";

$total_fijo = 0;
$total_saldo = 0;

foreach ($resultados as $fila) {
    $total_fijo += floatval($fila['arancel_fijo_cobrado']);
    $total_saldo += floatval($fila['monto_final_repa']);
    
    echo "<tr>
            <td>{$fila['id_cobro']}</td>
            <td>{$fila['nom_cliente']} {$fila['ape_cliente']}</td>
            <td>{$fila['tipo_electro_nombre']}</td>
            <td>{$fila['fecha_cobro_inicial']}</td>
            <td>{$fila['medio_pago_inicial']}</td>
            <td>{$fila['fecha_cobro_final']}</td>
            <td>{$fila['medio_pago_final']}</td>
            <td>{$fila['arancel_fijo_cobrado']}</td>
            <td>{$fila['monto_final_repa']}</td>
          </tr>";
}

// Totales
echo "<tr style='font-weight: bold; background-color: #f0f0f0;'>
        <td colspan='7'>Totales:</td>
        <td>$" . number_format($total_fijo, 2, ',', '.') . "</td>
        <td>$" . number_format($total_saldo, 2, ',', '.') . "</td>
      </tr>";
echo "</table>";
?>
