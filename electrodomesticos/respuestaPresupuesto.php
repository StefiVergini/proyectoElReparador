<?php
// respuestaPresupuesto.php
include_once __DIR__ . '/../conexionPDO.php';
$accion = $_GET['accion'] ?? '';
$token  = $_GET['token'] ?? '';

//se recibe el token por get y se busca en la bd
$stmt = $base->prepare("SELECT id_reparacion, id_cliente FROM mails WHERE token = ?");
$stmt->execute([$token]);
$emailLog = $stmt->fetch(PDO::FETCH_ASSOC);

if($emailLog) {
    $repairId = $emailLog['id_reparacion'];
    $idCli = $emailLog['id_cliente'];
    include_once '../clientes/clientes_class.php';
    $cliente = new Clientes($base);
    $cli = $cliente->obtenerUnCli($idCli);
    $nom_cli = $cli->getNomCli();
    $ape_cli = $cli->getApeCli();
    $mail_cli = $cli->getEmailCli();
    //-------------------------------------------------------------------------------------------
    //Buscamos al tecnico asignado a la reparación y empleado que cargo el electro
    include_once 'electro_class.php';
    $electro = new Electro($base);
    $reparaciones = $electro->leerReparaciones();
    $reparacionEncontrada = null;

    // Iteras sobre cada reparación para buscar la que coincida con $repairId.
    foreach ($reparaciones as $reparacion) {
        if ($reparacion->getIdReparacion() == $repairId) {
            $reparacionEncontrada = $reparacion;
            break;
        }
    }
    
    if ($reparacionEncontrada) {
        // Ahora tienes la reparación que coincide
        $tecnico = $reparacionEncontrada->getIdTecnico();
        $empAtencion = $reparacionEncontrada->getIdEmpAtencion();
        
        include_once '../notificaciones/notificaciones_class.php';
        $notificacion = new Notificacion($base);
        
        if ($accion === 'confirmar') {
            // Crear mensaje y link para la notificación
            $mensaje = "La Reparación #" . $repairId . " del cliente: " . $nom_cli . " " . $ape_cli . " ha sido Confirmada";
            $link = "/php/proyectoElReparador/electrodomesticos/inicioElectro.php";
            
            // Crear notificaciones para el técnico y el empleado de atención
            $notificacion->crearNoti($tecnico, $mensaje, $link);
            $notificacion->crearNoti($empAtencion, $mensaje, $link);
            //invalidar el token una vez que confirma o rechaza 
            $stmtUpdate = $base->prepare("UPDATE mails SET token = 'expirado' WHERE token = ?");
            $stmtUpdate->execute([$token]);
            
            echo "<script>alert('¡Presupuesto confirmado con éxito!'); window.close();</script>";
            exit;

        } elseif ($accion === 'rechazar') {
            $mensaje = "La Reparación #" . $repairId . " del cliente: " . $nom_cli . " " . $ape_cli . " ha sido Rechazada.";
            $link = "/php/proyectoElReparador/electrodomesticos/inicioElectro.php";
            
            $notificacion->crearNoti($tecnico, $mensaje, $link);
            $notificacion->crearNoti($empAtencion, $mensaje, $link);
            //invalidar el token una vez que confirma o rechaza 
            $stmtUpdate = $base->prepare("UPDATE mails SET token = 'expirado' WHERE token = ?");
            $stmtUpdate->execute([$token]);
            echo "<script>alert('Has rechazado el presupuesto. Contactate para retirar el Electrodoméstico.'); window.close();</script>";
            exit;
        } else {
            echo "Acción no válida.";
        }
    }
}else {
    echo "Token inválido o expirado.";
}


?>