<?php
include_once("../conexionPDO.php");
include_once("electro_class.php");

$electro = new Electro($base);
session_start();
// Recoger datos enviados
$id_reparacion = $_POST['id_reparacion'];
// Otras variables que necesites procesar, como id_cli, id_electro, nom_tipo, etc.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idCliente = $_POST["id_cli"];
    $idRepa = $_POST["id_reparacion"];
    $idElectro = $_POST["id_electro"];
    $marca = $_POST["marca"];
    $modelo = $_POST["modelo"];
    $nomTipo = $_POST["nom_tipo"];
    $email_cli = $_POST["email_cli"];
    $nom_cli = $_POST["nom_cli"];
    $comentCobro = $_POST["coment_cobro"];
    $comentCobro .= " - Presupuesto Rechazado - finaliza aquí la Reparación";
    
    
    

    if (isset($_POST['accion'])) {
        $accion = $_POST['accion'];
        
        if ($accion === 'si') {
            $fecha_fin_estimada = $_POST["fecha_fin_estimada"];
            $confirmPresup = 1;
            $estado_presu = "Presupuesto Confirmado";
            $estado_repa = 1;
            $electro->setConfirmaPresupuesto($confirmPresup);
            $electro->setEstadoPresu($estado_presu);
            $electro->setFechaFinEst( $fecha_fin_estimada);
            $electro->setEstadoReparacion($estado_repa);
            date_default_timezone_set("America/Argentina/Buenos_Aires");
            $fecha_ini = (new DateTime())->format("Y-m-d");
            //$hora_actual = (new DateTime())->format("H:i");
            $hora_actual = date("H:i");


            $resultado = $electro->confirmarPresupuesto($idRepa);
            if ($resultado) {
                // Datos del nuevo evento / reparación --> en CALENDARIO
                $_POST = [
                    "hora_ini" => $hora_actual,
                    "fecha_ini" => $fecha_ini,
                    "descripcion" => "Reparación ID $idRepa",
                    "hora_fin" => $hora_actual,
                    "fecha_fin" => $fecha_fin_estimada,
                    "electro" => "true",
                ];

                include_once '../calendario/agregar_evento.php';

                // Crear Notificación
                include_once '../notificaciones/notificaciones_class.php';
                $notificacion = new Notificacion($base);
                $tecnico= $_SESSION['id'];  
                $fecha_fin_format = date("d/m/Y", strtotime($fecha_fin_estimada));
                $mensaje = "Se ha confirmado la Reparación #$idRepa y se ha agregado al Calendario con Fecha de Fin: $fecha_fin_format";
                $link = "/php/proyectoElReparador/calendario/inicioCalendario.php";
                $notificacion->crearNoti($tecnico, $mensaje, $link);

                //ENVIAR ACÁ CORREO AL CLIENTE COMO RTA AUTOMATICA CON FECHA DE FIN ESTIMADA
                include_once 'respuestaAutomaticaConfirmacion.php';
                rtaAutomaticaConfirmacion($email_cli,$nom_cli,$idRepa, $nomTipo, $marca, $modelo, $fecha_fin_format);
                echo "<script>alert('Presupuesto confirmado y Reparación agregada en el Calendario - Enviado por correo al cliente'); window.location.href='inicioElectro.php';</script>";
                exit;   

            } else {
                echo "<script>alert('UPS! Ha ocurrido un error inesperado.'); window.location.href='inicioElectro.php';</script>";
            }
        }elseif ($accion === 'no') {
            $estado_presu = "Presupuesto Rechazado";
            $monto_final_repa = 0;
            $medio_pago = "-";
            $nro_compro = "-";
            
            $electro->setEstadoPresu($estado_presu);
            $electro->setMontoFinRepa($monto_final_repa);
            $electro->setMedioPagoFin($medio_pago);
            $electro->setNroComproFin( $nro_compro);
            $resultado = $electro->rechazarPresupuesto($idRepa, $comentCobro);
            if ($resultado) {
                 // Crear Notificación
                include_once '../notificaciones/notificaciones_class.php';
                $notificacion = new Notificacion($base);
                $tecnico= $_SESSION['id'];  
                $mensaje = "Se ha rechazado la Reparación #$idRepa - registrada en Historial de Presupuestos Rechazados";
                $link = "/php/proyectoElReparador/electrodomesticos/historialPresupuestoRechazado.php";
                $notificacion->crearNoti($tecnico, $mensaje, $link);

                //ENVIAR ACÁ CORREO AL CLIENTE COMO RTA AUTOMATICA RETIRO DEL ELECTRO
                include_once 'respuestaAutomaticaRechazo.php';
                rtaAutomaticaRechazo($email_cli,$nom_cli,$idRepa);
                echo "<script>alert('Presupuesto Rechazado - Cliente Notificado por Correo'); window.location.href='inicioElectro.php';</script>";
                exit;   
            } else {
                echo "<script>alert('UPS! Ha ocurrido un error inesperado.'); window.location.href='inicioElectro.php';</script>";
            }
        } else {
            echo "<script>alert('Acción inesperada. Inténtelo nuevamente'); window.location.href='inicioElectro.php';</script>";
        }
    } else{
        echo "<script>alert('No se han enviado los datos correctamente. Inténtelo nuevamente.'); window.location.href='inicioElectro.php';</script>";
    }
}
?>
