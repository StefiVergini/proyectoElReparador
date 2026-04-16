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
    $monto = $_POST["monto_cobrar"];
    $idElectro = $_POST["id_electro"];
    $marca =  $_POST["marca"];
    $modelo =  $_POST["modelo"];
    $nomTipo = $_POST["nom_tipo"];
    $estadoPresup = "Reparacion Finalizada";
    $estadoRepa = 0;
    $idEmpEnvia= $_SESSION['id'];

    //var_dump($_POST);

    $reparacion = new Electro($base);

    $reparacion->setEstadoReparacion($estadoRepa);
    $reparacion->setEstadoPresu($estadoPresup);

    $resultado = $reparacion->reparacionFinalizada($idRepa);

    if($resultado === true){
        $token = 'Sin token';
        $electroFull = $nomTipo . " " . $marca . " " . $modelo;
        //var_dump($electroFull);
        //exit;
        // insertar el correo enviado en mails            
        $stmt = $base->prepare("INSERT INTO mails (id_reparacion, idemp_envia,id_cliente,destinatario_mail, asunto, token) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$idRepa, $idEmpEnvia, $idCliente, $mailCliente, "Reparacion Finalizada", $token]);
        
        // Crear Notificación
        include_once '../notificaciones/notificaciones_class.php';
        $notificacion = new Notificacion($base);
        $mensaje = "Has Finalizado la Reparación #$idRepa !! y se ha finalizado la reparación en calendario";
        $link = "/php/proyectoElReparador/electrodomesticos/inicioElectro.php";
        if (!empty($idEmpEnvia) && !empty($mensaje) && !empty($link)) {
            $notificacion->crearNoti($idEmpEnvia, $mensaje, $link);
        } else {
            echo "Error: La notificación no pudo crearse por valores faltantes.";
        }



        $desc_evento = "Reparación ID ". $idRepa;
        $queryCalendar = $base->prepare("SELECT idcalendario FROM calendario WHERE descripcion_evento = ?");
        $queryCalendar->execute([$desc_evento]);
        $idCalendar = $queryCalendar->fetch(PDO::FETCH_ASSOC);

        $updateCalendar = $base->prepare("UPDATE calendario SET fecha_fin = NOW(), estado_evento = 0 WHERE idcalendario = ?");
        if ($idCalendar) {
            $updateCalendar->execute([$idCalendar['idcalendario']]);
        } else {
            echo "<h3 style='color: red;'>Error: No se encontró un evento en el calendario para la reparación ID $idRepa. Ha sido modificada.</h3>";
        }
        
        $_POST = [
            "enviar" => true,
            "email" => $mailCliente,
            "nombre" => "EL REPARADOR",
            "nombre_cli" => $nomCliente,
            "id_repa" => $idRepa, 
            "electrodomestico_full" => $electroFull,
            "monto" => $monto,
            "tipo_correo" => "Reparacion",
            "email-select" => $mailCliente,
            "asunto" => "Reparacion Finalizada",
        ];
        include_once '../mail/mail_inter.php'; 

        exit;

        //
    }else{
        echo "<script>alert('Ocurrio un error no se pudo enviar el correo.'); window.location.href='inicioElectro.php';</script>";
    }

   
}
?>
