<?php
include_once("../conexionPDO.php");
include_once("electro_class.php");

$electro = new Electro($base);
session_start();

$id_reparacion = $_POST['id_reparacion'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idCliente = $_POST["id_cli"];
    $mailCli = $_POST["email_cli"];
    $idRepa = $_POST["id_reparacion"];
    $idElectro = $_POST["id_electro"];
    $fechaIng = $_POST["fecha_ing"];
    $marca = $_POST["marca"];
    $modelo = $_POST["modelo"];
    $nomTipo = $_POST["nom_tipo"];
    $email_cli = $_POST["email_cli"];
    $nom_cli = $_POST["nom_cli"];
    //datos generales necesarios a completar
    $descRePresu = $_POST["descRePresu"];
    $materiales = $_POST["materiales"];
    $tecnico = $_POST['tecnico'];
    $presuDesc = "Materiales: ". $materiales . " Detalle de la Reparación: ".$descRePresu;
    $idEmpPresu= $_SESSION['id'];

    //materiales y descripcion en ambos, si se acepta garantia el presup es de 0 y sino se envia el monto

    if (isset($_POST['accion'])) {
        $accion = $_POST['accion'];
        
        if ($accion === 'si') {
            $estado_presu = "Presupuesto enviado";
            $presu = 0;
            $electro->setPresupuesto($presu);
            $electro->setIdEmpPresu($idEmpPresu);
            $electro->setObservaciones($presuDesc);
            $electro->setEstadoPresu($estado_presu);
            $electro->enviarPresupuesto($idRepa);

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
                $fecha_fin_format = date("d/m/Y", strtotime($fecha_fin_estimada));
                $mensaje = "Se ha confirmado la Garantía de la Reparación #$idRepa y se ha agregado al Calendario con Fecha de Fin: $fecha_fin_format";
                $link = "/php/proyectoElReparador/calendario/inicioCalendario.php";
                $notificacion->crearNoti($tecnico, $mensaje, $link);

                //ENVIAR ACÁ CORREO AL CLIENTE COMO RTA AUTOMATICA CON FECHA DE FIN ESTIMADA
                include_once 'respuestaAutomaticaConfirmGarantia.php';
                rtaAutomaticaConfirmacion($email_cli,$nom_cli,$idRepa, $nomTipo, $marca, $modelo, $fecha_fin_format);
                echo "<script>alert('Garantía confirmada y Reparación agregada en el Calendario - Enviado por correo al cliente'); window.location.href='inicioElectro.php';</script>";
                exit;   

            } else {
                echo "<script>alert('UPS! Ha ocurrido un error inesperado.'); window.location.href='inicioElectro.php';</script>";
            }
        }elseif ($accion === 'no') {
            //se genera un token de rta y se inserta en la bd
            $token = bin2hex(random_bytes(16));
            $stmt = $base->prepare("INSERT INTO mails (id_reparacion, idemp_envia,id_cliente,destinatario_mail, asunto, token) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$idRepa, $idEmpPresu, $idCliente, $mailCli, "Cotizacion de la Reparacion", $token]);


            //datos si no cubre la garantia
            $xqNoCubre = $_POST['xqNoCubre'] ?? ''; 
            $presup = isset($_POST['presup']) ? floatval($_POST['presup']) : 0; 
            $estado_presu = "Presupuesto enviado";
            //ed_coment
            $comentariosAnt = $_POST["ed_coment"] ?? '';
            $noCubre = trim($xqNoCubre) . ( $comentariosAnt ? ". " . $comentariosAnt : '' );

            $electro->setEstadoPresu($estado_presu);
            $electro->setObservaciones($presuDesc);
            $electro->setIdEmpPresu($idEmpPresu);
            $electro->setPresupuesto($presup);
            $electro->setIdElectro($idElectro);
            $electro->setEdFechaIng($fechaIng);
            $resultado = $electro->enviarPresupuesto($idRepa, $noCubre);
            if ($resultado) {
                 // Crear Notificación
                include_once '../notificaciones/notificaciones_class.php';
                $notificacion = new Notificacion($base);
                $tecnico= $_SESSION['id'];  
                $mensaje = "Se ha rechazado la Garantía de la Reparación #$idRepa - se envia por Correo el Presupuesto";
                $link = "/php/proyectoElReparador/electrodomesticos/inicioElectro.php";
                $notificacion->crearNoti($tecnico, $mensaje, $link);

                //ENVIAR ACÁ CORREO AL CLIENTE RESPUESTA POR QUE NO CUBRE LA GARANTÍA
                //Y EL VALOR DEL PRESUPUESTO PARA REALIZAR LA REPARACIÓN
                include_once 'rtaRechazoGarantEnvPresu.php';
                rtaAutomaticaConfirmacion($email_cli,$nom_cli,$idRepa, $nomTipo, $marca, $modelo,$xqNoCubre,$presup,$materiales,$descRePresu, $token);
                echo "<script>alert('Garantía Rechazada - Presupuesto Enviado - Cliente Notificado por Correo'); window.location.href='inicioElectro.php';</script>";
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