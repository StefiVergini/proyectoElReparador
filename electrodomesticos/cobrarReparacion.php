<?php
    include_once("../conexionPDO.php");
    include("electro_class.php");
    include_once("../header.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar datos del formulario
    $idRepa = $_POST["id_reparacion"];
    $idCliente = $_POST["id_cli"];
    $nomCliente = $_POST["nom_cli"];
    $mailCliente = $_POST["mail_cli"];
    $idElectro = $_POST["id_electro"];
    $marca =  $_POST["marca"];
    $modelo =  $_POST["modelo"];
    $nomTipo = $_POST["nom_tipo"];
    $medioPago = $_POST["medio_pago"];
    $nroComprobante = $_POST["nro_comprobante"];
    $comentCobro = $_POST["coment_cobro"];
    $comentariosAnt = $_POST["comentarios"];
    $comentariosTotal = $comentariosAnt . " ". $comentCobro;
    $estadoPresup = "Reparacion Cobrada";
    $montoACobrar = floatval(trim($_POST["monto_a_cobrar"]));
    $montoAbona   = floatval(trim($_POST["monto_abona"]));
    
    $idEmpEnvia= $_SESSION['id'];

   // echo "<pre>";
   // var_dump($_POST["monto_a_cobrar"], $_POST["monto_abona"]);
   // echo "</pre>";
   // exit;
    if ($montoACobrar != $montoAbona){
        echo "<script>alert('Error, el monto Cobrado es Distinto al que tiene que abonar. No entregue el Electrodoméstico hasta que sean iguales'); window.location.href='inicioElectro.php';</script>";
        exit;
    }else{
        $reparacion = new Electro($base);

        $reparacion->setMedioPagoFin($medioPago);
        $reparacion->setComentariosCobro($comentariosTotal);
        $reparacion->setNroComproFin($nroComprobante);
        $reparacion->setMontoFinRepa($montoAbona);
        $reparacion->setEstadoPresu($estadoPresup);

        $resultado = $reparacion->reparacionCobrada($idRepa);

        if($resultado === true){
            $token = 'Sin token';
            $electroFull = $nomTipo . " " . $marca . " " . $modelo;
            //var_dump($electroFull);
            //exit;
            // insertar el correo enviado en mails            
            $stmt = $base->prepare("INSERT INTO mails (id_reparacion, idemp_envia,id_cliente,destinatario_mail, asunto, token) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$idRepa, $idEmpEnvia, $idCliente, $mailCliente, "Reparacion Cobrada", $token]);
            
            // Crear Notificación
            include_once '../notificaciones/notificaciones_class.php';
            $notificacion = new Notificacion($base);
            $mensaje = "Se ha cobrado la reparacion #$idRepa - la podrás encontrar en Historial de Reparaciones";
            $link = "/php/proyectoElReparador/electrodomesticos/historialReparacionesCobradas.php";
            if (!empty($idEmpEnvia) && !empty($mensaje) && !empty($link)) {
                $notificacion->crearNoti($idEmpEnvia, $mensaje, $link);
            } else {
                echo "Error: La notificación no pudo crearse por valores faltantes.";
            }
            
            include_once 'respuestaCobroRepa.php';
            rtaAutomaticaCobro($mailCliente,$nomCliente,$idRepa, $nomTipo, $marca, $modelo, $montoAbona);
            echo "<script>alert('Reparación cobrada y enviada por correo al cliente'); window.location.href='inicioElectro.php';</script>";

            exit;

        }
    }
    
}else{
        echo "<script>alert('Ocurrio un error inesperado.'); window.location.href='inicioElectro.php';</script>";
}


?>
