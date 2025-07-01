<?php
include("../conexionPDO.php");

echo "\xEF\xBB\xBF";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=reporte_reparaciones.xls");

$rango = $_GET['rango'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$tecnico = $_GET['tecnicos'] ?? '';

echo "<table border='1'>";
echo "<tr>
        <th>ID</th>
        <th>Fecha Inicio</th>
        <th>Fecha Estimada Fin</th>
        <th>Fecha Finalización</th>
        <th>Diferencia</th>
        <th>Estado</th>
        <th>Tipo</th>
        <th>Marca</th>
        <th>Modelo</th>
        <th>N° Serie</th>
        <th>Técnico</th>
        <th>Descripción</th>
      </tr>";

if (!empty($rango)) {
    $fechas = explode(" to ", $rango);
    if (count($fechas) == 2) {
        $inicio = trim($fechas[0]);
        $fin = trim($fechas[1]);

        $sql = "SELECT r.id_reparacion, r.fecha_inicio, r.fecha_fin_estimada, r.fecha_finalizacion, r.estado_reparacion, 
                       t.nom_tipo, e.nom_empleado, e.ape_empleado,
                       el.marca, el.modelo, el.num_serie, a.observaciones
                FROM reparaciones r
                INNER JOIN electrodomesticos el ON r.idelectrodomesticos = el.idelectrodomesticos
                INNER JOIN tipo_electro t ON el.tipo_electro = t.idtipo_electro
                INNER JOIN empleados e ON r.id_tecnico = e.idempleados
                INNER JOIN atencion_presupuesto a ON r.id_reparacion = a.id_reparacion
                WHERE r.fecha_inicio BETWEEN :inicio AND :fin";

        if (!empty($tipo)) $sql .= " AND el.tipo_electro = :tipo";
        if (!empty($tecnico)) $sql .= " AND r.id_tecnico = :tecnico";

        $stmt = $base->prepare($sql);
        $stmt->bindParam(':inicio', $inicio);
        $stmt->bindParam(':fin', $fin);
        if (!empty($tipo)) $stmt->bindParam(':tipo', $tipo);
        if (!empty($tecnico)) $stmt->bindParam(':tecnico', $tecnico);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultados as $fila) {
            $fechaEstimada = $fila['fecha_fin_estimada'];
            $fechaFinal = $fila['fecha_finalizacion'];
            $diferenciaTexto = '—';

            if ($fechaEstimada && $fechaFinal) {
                $f1 = new DateTime($fechaEstimada);
                $f2 = new DateTime($fechaFinal);
                $dias = $f1->diff($f2)->days;

                if ($f2 > $f1) {
                    $diferenciaTexto = $dias . " días de atraso";
                } elseif ($f2 < $f1) {
                    $diferenciaTexto = " " . $dias . " días antes";
                } else {
                    $diferenciaTexto = "0 días de diferencia";
                }
            }

            echo "<tr>
                    <td>{$fila['id_reparacion']}</td>
                    <td>{$fila['fecha_inicio']}</td>
                    <td>" . ($fechaEstimada ?? '-') . "</td>
                    <td>" . ($fechaFinal ?? '-') . "</td>
                    <td>{$diferenciaTexto}</td>
                    <td>{$fila['estado_reparacion']}</td>
                    <td>{$fila['nom_tipo']}</td>
                    <td>{$fila['marca']}</td>
                    <td>{$fila['modelo']}</td>
                    <td>{$fila['num_serie']}</td>
                    <td>{$fila['nom_empleado']} {$fila['ape_empleado']}</td>
                    <td>" . nl2br(htmlspecialchars($fila['observaciones'])) . "</td>
                  </tr>";
        }
    }
}

echo "</table>";
?>
