<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("empleados_class.php");

    $busquedaRealizada = false;
    
    $empleadosModel = new Empleados($base);
    //$empleadosModel->agregarColumnaSiNoExiste('historial_empleados', 'descripcion_cambio', 'VARCHAR(255) NULL');


    $empleados = [];
    // Obtener todos los empleados
    $estado = 0;
    $empleados = $empleadosModel->leerEmpleados($estado);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/tablas.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
    

</head>
<body>
    <main>
        <h1 class="titulo">Antiguos Empleados</h1>
        <div class="table-container">
            <table class="tabla">
                <tr>
                    <th class="tabla-head">Id</th>
                    <th class="tabla-head">DNI</th>
                    <th class="tabla-head">Nombre</th>
                    <th class="tabla-head">Apellido</th>
                    <th class="tabla-head">Teléfono</th>
                    <th class="tabla-head">Email</th>
                    <th class="tabla-head">Sucursal</th>
                    <th class="tabla-head">Fecha de Inicio</th>
                    <th class="tabla-head">Fecha Finalizacion</th>
                    <th class="tabla-head">Categoría</th>
                    <th class="tabla-head">Tipo</th>
                    <th class="tabla-head">Descripción</th>
                    <?php
                        if($rol == 2 || $rol == 4 || $rol == 5 || $rol == 6){
                        ?>
                        <th class="tabla-head">Reincorporar</th>
                        <?php
                        }
                    ?>
                </tr>
                <?php
                    if (!empty($empleados)){
                        foreach ($empleados as $emp) {
                            $empleado = $emp['empleado'];
                            $historial = $emp['historial'];
                            $categoria = $emp['categoria'];
                            echo "<tr>";
                            echo "<td class='tabla-data'>" . $empleado->getIdEmp() . "</td>";
                            echo "<td class='tabla-data'>" . $empleado->getDniEmp() . "</td>";
                            echo "<td class='tabla-data'>" . $empleado->getNomEmp() . "</td>";
                            echo "<td class='tabla-data'>" . $empleado->getApeEmp() . "</td>";
                            echo "<td class='tabla-data'>" . $empleado->getTelEmp() . "</td>";
                            echo "<td class='tabla-data'>" . $empleado->getEmailEmp() . "</td>";
                            echo "<td class='tabla-data'>" . $empleado->getIdLocal() . "</td>";
                            $fecha_inicio = $historial->getFechaInicio();
                            $fecha_inicio_mysql = date("d-m-Y", strtotime($fecha_inicio));
                            echo "<td class='tabla-data'>" . $fecha_inicio_mysql . "</td>";
                            $fecha_fin = $historial->getFechaFin();
                            $fecha_fin_mysql = date("d-m-Y", strtotime($fecha_fin));
                            echo "<td class='tabla-data'>" . $fecha_fin_mysql . "</td>";
                            echo "<td class='tabla-data'>" . $historial->getIdCategoria() . "</td>";
                            echo "<td class='tabla-data'>" . $categoria->getTipoEmp() . "</td>";
                            $desc = $historial->getDescripcion();
                            if($desc != null){
                                echo "<td class='tabla-data'>" . $desc . "</td>";
                            }else{
                                echo "<td class='tabla-data'>     -     </td>";
                            }
                            
                            if($rol == 2 || $rol == 4 || $rol == 5 || $rol == 6){
                            
                                echo "<td class='tabla-data'>
                                        <form action='actualizarEmp.php' method='post' style='display:inline;'  onsubmit='return confirmarActualizacion(\"" . $empleado->getNomEmp() . "\", \"" . $empleado->getDniEmp() . "\")'>
                                                <input type='hidden' name='id' value='" . $empleado->getIdEmp() . "'>
                                                <button type='submit' class='btn-iconos'>
                                                    <img src='../static/images/actualizar.png' alt='actualizar' title='Actualizar Empleado' width='20' height='20'>
                                                </button>
                                            </form>
                                        </td>";
                                        
                            }
                                
                    
                            echo "</tr>";
                        }
                    } else {
                        echo "<h1 class='titulo'>No hay datos</h1>";
                    }
                ?>
            </table>
        </div>
            <br><button class='btn' style="display:block; margin-left: auto; margin-right:auto;"><a style='text-decoration:none; color:white;' href='inicioEmp.php'>Empleados</a></button><br>


    </main>
    <?php
    require "../footer.php";
    ?>
</body>
</html>