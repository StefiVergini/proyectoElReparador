<?php
include("../header.php");
include("../conexionPDO.php");
include("../electrodomesticos/electro_class.php");
include("../empleados/empleados_class.php");
?>

<!DOCTYPE html>
<html lang="es">

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <h1 class="titulo">Estadísticas de Técnicos</h1>

    <form class="form-reporte" method="POST" action="" id="formFiltro">
        <label for="rango">Selecciona un rango de fechas:</label>
        <input type="text" id="rango" name="rango"
            placeholder="DD-MM-YYYY para DD-MM-YYYY"
            value="<?php echo isset($_POST['rango']) ? htmlspecialchars($_POST['rango']) : ''; ?>">


        <div style="margin-top: 10px;">
            <button type="submit">Filtrar</button>
            <button type="button" id="limpiar">Limpiar</button>
        </div>
    </form>

    <?php
    $labels = [];
    $data = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["rango"])) {
        $fechas = explode(" to ", $_POST["rango"]);
        if (count($fechas) == 2) {
            $fecha_inicio = trim($fechas[0]);
            $fecha_fin = trim($fechas[1]);

            echo "<div class='mensaje-reporte'>Mostrando reparaciones por técnico desde <b>$fecha_inicio</b> hasta <b>$fecha_fin</b></div>";

            $fecha_inicio = DateTime::createFromFormat('d-m-Y', trim($fechas[0]))->format('Y-m-d');
            $fecha_fin = DateTime::createFromFormat('d-m-Y', trim($fechas[1]))->format('Y-m-d');
            $query = "
            SELECT empleados.nom_empleado, empleados.ape_empleado, COUNT(*) AS cantidad FROM empleados JOIN reparaciones ON reparaciones.id_tecnico = empleados.idempleados WHERE reparaciones.fecha_finalizacion BETWEEN :inicio AND :fin GROUP by empleados.idempleados;
            ";

            /*$query = "
            SELECT 
                empleados.nom_empleado, empleados.ape_empleado, COUNT(*) AS cantidad
            FROM reparaciones
            JOIN empleados ON reparaciones.id_tecnico = empleados.idempleados
            GROUP BY empleados.idempleados
            ";*/
            $stmt = $base->prepare($query);
            $stmt->bindParam(':inicio', $fecha_inicio);
            $stmt->bindParam(':fin', $fecha_fin); 
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($resultados) {
                echo "<table class='tabla'>";
                echo "<thead><tr><th>Técnico</th><th>Reparaciones</th></tr></thead><tbody>";
                foreach ($resultados as $fila) {
                    $nombreCompleto = "{$fila['nom_empleado']} {$fila['ape_empleado']}";
                    $labels[] = $nombreCompleto;
                    $data[] = $fila['cantidad'];
                    echo "<tr><td><strong style='color:#333;'>$nombreCompleto</strong></td><td>{$fila['cantidad']}</td></tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p style='color:red; text-align:center;'>No se encontraron reparaciones en ese rango de fechas.</p>";
            }
        } else {
            echo "<p style='color:red; text-align:center;'>Formato de fecha no válido.</p>";
        }
    }
    ?>

    <!-- Flatpickr -->
    <script>
        flatpickr("#rango", {
            mode: "range",
            dateFormat: "d-m-Y"
        });

        document.getElementById("limpiar").addEventListener("click", function() {
            document.getElementById("rango").value = "";
            document.getElementById("formFiltro").submit(); // Envía el formulario para limpiar resultados
        });
    </script>

    <!-- Gráfico de barras -->
    <?php if (!empty($labels) && !empty($data)): ?>
        <div style="max-width: 800px; margin: 3rem auto;">
            <canvas id="graficoTecnicos">
            </canvas>
        </div>

        <script>
            const ctx = document.getElementById('graficoTecnicos').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Cantidad de reparaciones por técnico',
                        data: <?php echo json_encode($data); ?>,
                        backgroundColor: 'rgba(30, 90, 180, 0.8)',
                        borderColor: 'rgba(30, 90, 180, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Reparaciones por Técnico'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        </script>
    <?php endif; ?>

    <?php require '../footer.php'; ?>
</body>
<script>
    document.getElementById('formFiltro').addEventListener('submit', function(e) {
        const rango = document.getElementById('rango').value.trim();
        if (!rango) {
            e.preventDefault(); // Detiene el envío del formulario
            alert('La selección de un rango de fechas es obligatoria.');
            document.getElementById('rango').focus();
        }
    });
</script>

</html>