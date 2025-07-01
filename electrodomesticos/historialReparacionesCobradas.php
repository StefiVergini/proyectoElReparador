<?php
include_once("../header.php");
include_once("../conexionPDO.php");
include_once("electro_class.php");

$electro = new Electro($base);

$rol_tecnico = $_SESSION['rol'];

// Inicializar filtros
$desde = '';
$hasta = '';
$clienteFiltro = '';
$busquedaRealizada = false;

// Por defecto, mostramos todas las reparaciones
$reparaciones = $electro->leerReparaciones();
$filtro_repas =[];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desde         = $_POST['desde'] ?? '';
    $hasta         = $_POST['hasta'] ?? '';
    $clienteFiltro = $_POST['dni']   ?? '';

    if ((!empty($desde) && !empty($hasta)) || !empty($clienteFiltro)){
        $reparaciones = $electro->filtrarHistorialReparaciones($desde, $hasta, $clienteFiltro);
        $busquedaRealizada = true;
    }else{
        $filtro_repas = $electro->leerReparaciones();
        if ((!empty($desde) && empty($hasta)) || (empty($desde) && !empty($hasta))) {
                $error = "Si deseas filtrar por fechas, debes ingresar ambas.";
                ?> <h2 style="grid-column: 1 / -1; text-align:center; color:red; background-color:white;"><?php echo $error;?> </h2><?php
            }
    }


}else{
    $reparaciones = $electro->leerReparaciones();
    $busquedaRealizada = false;
 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Electrodomesticos</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/card.css" />
    <script src="../static/js/funciones_empleados.js"></script>
    <script src="../static/js/funciones_select_nav.js"></script>
    <style>
            .section-btns {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 20px;
            }

            .section-btns button,
            .section-btns form {
                margin: 0;
            }

            .grid-container {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 3fr));
                gap: 40px;
                padding: 40px;
            }

            /* Estilo para los mensajes de sin resultados */
            .no-results-message {
                grid-column: 1 / -1;
                text-align: center;
                margin-top: 20px;
            }
            
            .reject-btn{
                background-color:#f45752;
            }
            .reject-btn:hover{
                background-color: red;
            }
    </style>
    <script>
        function mostrarFiltros(filtroId) {
            event.preventDefault();
            var filtros = document.getElementById(filtroId);
            // Puedes agregar aquí algún ajuste al container de la cuadrícula si es necesario
            if (filtros.style.display === 'none' || filtros.style.display === '') {
                filtros.style.display = 'block';
            } else {
                filtros.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <h1 class="titulo">Historial de Reparaciones Cobradas</h1>
    <hr>
    <!-- Reparaciones Cobradas -->
    <div id="repaCobradas" class="grid-container" >
        <div class="div-con-botones" style="grid-column: 1 / -1; text-align:center;">
            <form action="inicioElectro.php" method="post">
                <button class='btn'> Electrodomesticos
                    <a href="inicioElectro.php"></a>
                </button>
            </form>
            <button class='btn' id="btn-filtro" type="button" onclick="mostrarFiltros('filtrosRepa')">
                Filtros
                <img src='../static/images/filter.png' alt='filtro' title='Filtrar' width='23' height='23'>
            </button>
        </div>

        <div class="div-con-botones" id="filtrosRepa" style="grid-column: 1 / -1; text-align:center; margin-bottom:55px; display: none;">
            <form action="" method="post">
                <div class="form-group">
                    <h2 style="margin:5px;">Filtros elija por fecha o por cliente o ambos: </h2>
                    <h4>Se calcula entre fechas de ingreso de los electrodomésticos</h4>
                    <div id="desde" style="display: inline-block;">
                        <label class="label" for="desde">Fecha Desde: </label>
                        <input class="input" type="date" name="desde"><br>
                    </div>
                    <div id="hasta" style="display: inline-block;">
                        <label class="label" for="hasta">Fecha Hasta: </label>
                        <input class="input" type="date" name="hasta"><br>
                    </div>
                    <div id="filtro_cliente">
                        <label class="label" for="dni">Ingrese parte del Nombre o Apellido del Cliente: </label>
                        <input class="input" type="text" name="dni">
                        <button class="btn-iconos" type="submit">
                            <img src="../static/images/lupa.png" alt="Buscar" width="30" height="20" />
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        $contador_reparaciones = 0;
        foreach ($reparaciones as $rep):
            if ($rep->getEstadoPresu() == 'Reparacion Cobrada' ):  // En reparación
                $contador_reparaciones++;
                $fecha_inicio = date("d/m/Y", strtotime($rep->getFechaInicio()));
                $fecha_fin = date("d/m/Y", strtotime($rep->getFechaFin()));
                $fecha_retiro_electro = date("d/m/Y", strtotime($rep->getFechaDeRetiro()));
                $fecha_fin_garantia = date("d/m/Y", strtotime($rep->getFechaFinGarantia()));
                $descPresu = $rep->getObservaciones();
                $partes = explode(" Detalle de la Reparación: ", $descPresu);

                // La primera parte contiene "Materiales: ..."
                $materiales = trim(str_replace("Materiales: ", "", $partes[0]));
                                
                // La segunda parte contiene el detalle de la reparación
                $detalleReparacion = trim($partes[1]);
                $presup = $rep->getPresupuesto();
                $monto_fijo = $rep->getMontoFijoIni();
                $cant_a_abonar = $presup - $monto_fijo;
                $medio_pago_fin = $rep->getMedioPagoFin();
        ?>
            <div class="card" onclick="expandirDetalle(<?= $rep->getIdReparacion() ?>)">

                <div class="card-header">Reparación Cobrada #<?= $rep->getIdReparacion() ?></div>
                <ul class="list-group">
                    <li class="list-group-item centered"><strong style="color:red;">Garantía Válida hasta:</strong> <?= $fecha_fin_garantia ?></li>
                    <li class="list-group-item centered"><strong>Fecha Retiro Electro:</strong> <?= $fecha_retiro_electro ?> </li>
                    <li class="list-group-item"><strong>Monto Abonado:</strong> <strong style="color:red;">$<?= $rep->getMontoFinRepa() ?> </strong> <strong>Medio de pago: </strong> <?= ucwords($medio_pago_fin) ?> </li>
                     <li class="list-group-item"><strong>Electrodoméstico:</strong> <?= ucwords($rep->getNomTipo()) . " - " . strtoupper($rep->getMarca()) ?><br>Modelo: <?= $rep->getModelo() ?></li>
                     <li class="list-group-item"><strong>Cliente:</strong> <?= ucwords($rep->getNomCli() . " " . $rep->getApeCliente()) ?></li>
                    <li class="list-group-item"><strong>Técnico Asignado:</strong> <?= ucwords($rep->getNomTecnico() . " " . $rep->getApeTecnico()) ?></li>
                    <li class="list-group-item" style="text-align: center;"><strong>Más Detalles</strong></li>
                    <li class="list-group-item" style="text-align: center;"><strong>&#9207;</strong></li>
                    
                </ul>
            </div>
        <?php endif; endforeach; ?>

        <?php if ($contador_reparaciones == 0): ?>
            <div style="grid-column: 1 / -1; text-align: center;">
                <h3>No hay Historial de Reparaciones Cobradas</h3>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Overlay para la tarjeta expandida -->
    <div class="overlay" id="overlay">
        <div class="expanded-card" id="detalle-expandido">
            <button class="close-btn" onclick="cerrarDetalle()">X</button>
            <div id="contenido-reparacion"></div>
        </div>
    </div>
    <?php
     if($busquedaRealizada){
            echo "<br><button class='btn' style='display:block; margin-left: auto; margin-right:auto; margin-bottom: 15px;'><a style='text-decoration:none; color:white;' href='historialReparacionesCobradas.php'>Volver</a></button>";
        }
    ?>
            
    <script>

      

        //Expandir tarjetas

        function expandirDetalle(id) {
            let overlay = document.getElementById("overlay");
            let contenido = document.getElementById("contenido-reparacion");

            // Obtener los datos de la reparación seleccionada
            <?php foreach ($reparaciones as $reparacion) : ?>
                if (id === <?= $reparacion->getIdReparacion() ?>) {
                    <?php  $fecha_ing_electro = date("d/m/Y", strtotime($reparacion->getFechaIngElectro()));?>
                    contenido.innerHTML = `
                        <div class="card-header">
                            Reparación #<?= $reparacion->getIdReparacion() ?>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Fecha que Ingresó el Electrodoméstico:</strong> <?= $fecha_ing_electro ?></li>
                            <li class="list-group-item"><strong>Electrodoméstico:</strong> <?= strtoupper($reparacion->getMarca()) . " " . $reparacion->getModelo() ?> <?= "<br>Tipo: ". ucwords($reparacion->getNomTipo()) ."<br>N° de Serie: ". $reparacion->getNumSerie() ?></li>
                            <li class="list-group-item"><strong>Cliente:</strong> <?= "<br>Nombre: ". ucwords($reparacion->getNomCli() . " " . $reparacion->getApeCliente())."<br>Email: ". $reparacion->getEmailCliente(). "<br><strong>Problema según el Cliente: </strong>". ucwords($reparacion->getDescripcion()) ?></li>
                            <li class="list-group-item"><strong>Técnico Asignado:</strong> <?= ucwords($reparacion->getNomTecnico() . " " . $reparacion->getApeTecnico()) ?></li>
                        </ul>
                        <?php if ($reparacion->getEstadoPresu() == 'Reparacion Cobrada'):?>
                            <?php 
                            $fecha_inicio = date("d/m/Y", strtotime($reparacion->getFechaInicio()));
                            $fecha_fin = date("d/m/Y", strtotime($reparacion->getFechaFin()));
                            $fecha_retiro_electro = date("d/m/Y", strtotime($reparacion->getFechaDeRetiro()));
                            $fecha_fin_garantia = date("d/m/Y", strtotime($reparacion->getFechaFinGarantia()));
                            $descPresu = $reparacion->getObservaciones();
                            $partes = explode(" Detalle de la Reparación: ", $descPresu);

                            // La primera parte contiene "Materiales: ..."
                            $materiales = trim(str_replace("Materiales: ", "", $partes[0]));
                                            
                            // La segunda parte contiene el detalle de la reparación
                            $detalleReparacion = trim($partes[1]);
                            $presup = $reparacion->getPresupuesto();
                            $monto_fijo = $reparacion->getMontoFijoIni();
                            $medio_pago_ini =$reparacion->getMedioPagoIni();
                            $medio_pago_fin =$reparacion->getMedioPagoFin();

                        ?>
                            <ul class="list-group">
                                <li class="list-group-item centered"><strong>Fecha Retiro Electro:</strong> <?= $fecha_retiro_electro ?> &nbsp; <strong style="color:red;">Garantía Válida hasta:</strong> <?= $fecha_fin_garantia ?></li>
                                <li class="list-group-item centered"><strong>Fecha Inicio:</strong> <?= $fecha_inicio ?> &nbsp; <strong>Fecha Fin:</strong> <?= $fecha_fin ?></li>
                                <li class="list-group-item"><strong>Monto Abonado:</strong> <strong style="color:red;">$<?= $reparacion->getMontoFinRepa() ?> </strong> <strong>Medio de pago: </strong><?= ucwords($medio_pago_fin) ?> </li>
                                <li class="list-group-item"><strong>Presupuesto:</strong> $<?= $presup ?></li>
                                <li class="list-group-item"><strong>Arancel Inicio Abonado:</strong> $<?= $monto_fijo ?> <strong>Medio de pago: </strong><?= ucwords($medio_pago_ini)?></li>
                            
                                <li class="list-group-item"><strong>Detalle de la reparación a realizada:</strong> <?= $detalleReparacion  ?></li>
                                <li class="list-group-item"><strong>Materiales a utilizados:</strong> <?= ucwords($materiales) ?></li>
                                    
                            </ul> 
                       
                        <?php endif;?>
                       
                    `;
                }
            <?php endforeach; ?>
            
            overlay.style.display = "flex";  // Mostrar el overlay centrado
        }

        function cerrarDetalle() {
            document.getElementById("overlay").style.display = "none";
        }    

        
    </script>


</body>
    <?php include_once("../footer.php"); ?>
</html>
