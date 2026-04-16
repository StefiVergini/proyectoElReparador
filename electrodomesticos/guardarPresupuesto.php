<?php
    include_once("../conexionPDO.php");
    include("electro_class.php");
    include_once("../header.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar datos del formulario
    $idCliente = $_POST["id_cli"];
    $idRepa = $_POST["id_reparacion"];
    $monto = $_POST["presup"];
    $materiales = $_POST["materiales"];
    $descRePresu = $_POST["descRePresu"];
    $idElectro = $_POST["id_electro"];
    $presuDesc = "Materiales: ". $materiales . " Detalle de la Reparación: ".$descRePresu;
    $nomTipo = $_POST["nom_tipo"];
    $idEmpPresu= $_SESSION['id'];
    $estado = "Presupuesto enviado";

    $reparacion = new Electro($base);

    $reparacion->setIdEmpPresu($idEmpPresu);
    $reparacion->setPresupuesto($monto);
    $reparacion->setObservaciones($presuDesc);
    $reparacion->setEstadoPresu($estado);

    $resultado = $reparacion->enviarPresupuesto($idRepa);

    if($resultado === true){
        
        // Obtener datos del cliente y electrodoméstico
        $queryCliente = $base->prepare("SELECT nom_cliente, email_cliente FROM clientes WHERE idclientes = ?");
        $queryCliente->execute([$idCliente]);
        $cliente = $queryCliente->fetch(PDO::FETCH_ASSOC);
        //generar token para enviar correo y recibir rta del cliente
        $token = bin2hex(random_bytes(16));

        $queryElectro = $base->prepare("SELECT marca, modelo FROM electrodomesticos WHERE idelectrodomesticos = ?");
        $queryElectro->execute([$idElectro]);
        $electrodomestico = $queryElectro->fetch(PDO::FETCH_ASSOC);
        $electroFull = $nomTipo . " " . $electrodomestico['marca'] . " " . $electrodomestico['modelo'];
        if ($cliente && $electrodomestico) {
             // insertar el correo enviado en mails            
            $stmt = $base->prepare("INSERT INTO mails (id_reparacion, idemp_envia,id_cliente,destinatario_mail, asunto, token) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$idRepa, $idEmpPresu, $idCliente, $cliente["email_cliente"], "Cotizacion de la Reparacion", $token]);
            // Enviar el correo automáticamente
            //"tipo_correo" => "Presupuesto",
            $_POST = [
                "enviar" => true,
                "email" => $cliente["email_cliente"],
                "nombre" => "EL REPARADOR",
                "nombre_cli" => $cliente["nom_cliente"],
                "id_repa" => $idRepa, 
                "electrodomestico_full" => $electroFull,
                "monto" => $monto,
                "materiales" => $materiales,
                "descRe" => $descRePresu,
                "tipo_correo" => "Presupuesto",
                "email-select" => $cliente["email_cliente"],
                "asunto" => "Cotizacion de la Reparacion",
                "token" => $token,
            ];


            include_once '../mail/mail_inter.php';
            exit;

            
        }else{
            echo "<script>alert('Ocurrio un error no se pudo enviar el correo.'); window.location.href='inicioElectro.php';</script>";
        }

    }else{
        echo $resultado;
    }

   
}
?>
