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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body>
    <h1 class="titulo">Reportes de Reparaciones </h1>
    <form method="GET" action="" class="form-reporte" id="formFiltro">
        <label  class="label" for="rango">Selecciona un rango de fechas:</label>
        <input class="input-reportes" type="text" id="rango" name="rango" placeholder="YYYY-MM-DD to YYYY-MM-DD" value="<?php echo htmlspecialchars($_GET['rango'] ?? '')?>">

        <div class="formulario-contenedor">
            <label class="label" for="tipo">Tipo de Electrodoméstico: 
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

        <div class="formulario-contenedor">
            <label class="label" for="tecnicos">Técnico: 
                <select class="input-reportes" name="tecnicos" id="tecnicos">
                <option value="">Todos</option>
        <?php 
            $empleados = new Empleados($base);
            $empleado = $empleados->tecnicosXLocal($local);
            foreach ($empleado as $tecnicoObj) {
                $tec = $tecnicoObj->getIdEmp();
                $nombreTecnico = $tecnicoObj->getNomEmp()." ". $tecnicoObj->getApeEmp();
                $selected = ($tec == ($_GET['tecnicos'] ?? '')) ? 'selected' : '';
                echo "<option value=\"$tec\" $selected>" . ucwords($nombreTecnico) . "</option>";
            }
        ?>
                </select>
            </label>
        <?php
        $queryString = http_build_query([
            'rango' => $_GET['rango'] ?? '',
            'tipo' => $_GET['tipo'] ?? '',
            'tecnicos' => $_GET['tecnicos'] ?? ''
            ]);
        ?>
        </div>  
            <!--<div style="display: flex; align-items: flex-start; gap: 1rem; margin: 30px 20px;">
                <button class="btn-reportes" type="submit">Filtrar</button>-->
            <div style="margin-top: 30px;">
                <button type="submit">Filtrar</button>
                <button type="button" id="limpiar">Limpiar</button>
            </div>       

    </form>

    <?php
if ($_SERVER["REQUEST_METHOD"] == "GET" && !empty($_GET["rango"])) {
    $fechas = explode(" to ", $_GET["rango"]);
    $tipo = $_GET["tipo"] ?? "";
    $tecnico = $_GET["tecnicos"] ?? "";

    if (count($fechas) == 2) {
        $fecha_inicio = trim($fechas[0]);
        $fecha_fin = trim($fechas[1]);
        
        $sql = "SELECT r.id_reparacion, r.fecha_inicio, r.fecha_fin_estimada, r.fecha_finalizacion, a.estado_presup, 
                t.nom_tipo, e.nom_empleado, e.ape_empleado,
                el.marca, el.modelo, el.num_serie, a.observaciones
                FROM reparaciones r
                INNER JOIN electrodomesticos el ON r.idelectrodomesticos = el.idelectrodomesticos
                INNER JOIN tipo_electro t ON el.tipo_electro = t.idtipo_electro
                INNER JOIN atencion_presupuesto as a ON r.id_reparacion = a.id_reparacion
                INNER JOIN empleados e ON r.id_tecnico = e.idempleados
                WHERE r.fecha_inicio BETWEEN :inicio AND :fin";

        if (!empty($tipo)) {
            $sql .= " AND el.tipo_electro = :tipo";
        }

        if (!empty($tecnico)) {
            $sql .= " AND r.id_tecnico = :tecnico";
        }

        $stmt = $base->prepare($sql);
        $stmt->bindParam(':inicio', $fecha_inicio);
        $stmt->bindParam(':fin', $fecha_fin);

        if (!empty($tipo)) {
            $stmt->bindParam(':tipo', $tipo);
        }

        if (!empty($tecnico)) {
            $stmt->bindParam(':tecnico', $tecnico);
        }

        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($resultados) {
            echo "<div style='text-align: center; margin-top:40px;'>
                    <strong style='color: black;'>Mostrando resultados desde el $fecha_inicio hasta el $fecha_fin</strong>
                </div>";
            
            ?>
                <div class="div-con-botones">
                    <div class="desplegable" style='text-align: center; margin-top:40px;'>
                        <button class="btn-reportes">Exportar &#9207;</button>
                            <div class="link">
                                <a target="_blank" href="reportepdf.php?<?php echo $queryString; ?>">PDF</a>
                                <a class="excel" href="reporteexcel.php?<?php echo $queryString; ?>">Excel</a>
                            </div>
                    </div>
                </div>
            <?php
            echo "<div class='table-container'>";
            echo "<table class='tabla'>";
            echo "<thead>
                <tr>
                    <th class='tabla-head' >ID Reparación</th>
                    <th class='tabla-head' >Fecha Inicio</th>
                    <th class='tabla-head' >Fecha Estimada Fin</th>
                    <th class='tabla-head' >Fecha Finalización</th>
                    <th class='tabla-head' >Días de Diferencia</th> <!-- NUEVA COLUMNA -->
                    <th class='tabla-head' >Estado</th>
                    <th class='tabla-head' >Tipo Electro</th>
                    <th class='tabla-head' >Marca</th>
                    <th class='tabla-head' >Técnico</th>
                    <th class='tabla-head' >Descripción</th>
                </tr>
            </thead>";


            echo "<tbody>";
                foreach ($resultados as $fila) {
    $fechaEstimada = $fila['fecha_fin_estimada'];
    $fechaReal = $fila['fecha_finalizacion'];
    $diferenciaTexto = '—';

    if ($fechaEstimada && $fechaReal) {
        $fecha1 = new DateTime($fechaEstimada);
        $fecha2 = new DateTime($fechaReal);
        $dias = $fecha1->diff($fecha2)->days;

        // Si la reparación se terminó después de la fecha estimada
        if ($fecha2 > $fecha1) {
            $diferenciaTexto = $dias . " días de atraso";
        } elseif ($fecha2 < $fecha1) {
            $diferenciaTexto = "-" . $dias . " días antes";
        } else {
            $diferenciaTexto = "0 días de diferencia";
        }
    }

    echo "<tr>";
    echo "<td class='tabla-data' >{$fila['id_reparacion']}</td>";
    echo "<td class='tabla-data' >{$fila['fecha_inicio']}</td>";
    echo "<td class='tabla-data' >" . ($fechaEstimada ?? '-') . "</td>";
    echo "<td class='tabla-data' >" . ($fechaReal ?? '-') . "</td>";
    echo "<td class='tabla-data' >$diferenciaTexto</td>";
    echo "<td class='tabla-data' >" . ucwords($fila['estado_presup']) . "</td>";
    echo "<td class='tabla-data' >" . ucwords($fila['nom_tipo']) . "</td>";
    echo "<td class='tabla-data' >" . htmlspecialchars($fila['marca']) . "</td>";
    echo "<td class='tabla-data' >" . ucwords($fila['nom_empleado'] . " " . $fila['ape_empleado']) . "</td>";
    echo "<td class='tabla-data' >" . nl2br(htmlspecialchars($fila['observaciones'])) . "</td>";
    echo "</tr>";
}


            echo "</tbody></table></div>";

        } else {
            echo "<p>No se encontraron resultados para los filtros seleccionados.</p>";
        }

    } else {
        echo "<p style='color:red;'>Formato de fecha no válido.</p>";
    }
}
?>
    
    <script>
        flatpickr("#rango", {
            mode: "range",
            dateFormat: "Y-m-d"
        });
    </script>

<?php
    $queryString = http_build_query([
        'rango' => $_GET['rango'] ?? '',
        'tipo' => $_GET['tipo'] ?? '',
        'tecnicos' => $_GET['tecnicos'] ?? ''
    ]);
?>
<?php require '../footer.php'; ?>
</body>
<script>
    document.getElementById("limpiar").addEventListener("click", function() {
            document.getElementById("rango").value = "";
            document.getElementById("tipo").value = "";
            document.getElementById("tecnicos").value = "";
            document.getElementById("formFiltro").submit(); // Envía el formulario para limpiar resultados
        });
</script>
</html>
