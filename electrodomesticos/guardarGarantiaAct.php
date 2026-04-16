<?php
include("../conexionPDO.php");
include("electro_class.php");

session_start();
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
//exit;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cli = $_POST['idCli'];
    $idElectro = $_POST['idElectro'];
    $idRepaAnt = $_POST['idRepaAnt'];
    $desc = $_POST['desc'];
    $tecnico = $_POST['tecnicos']; 
    $idEmp= $_SESSION['id'];
    $estado_repa = 0;
    $estado_presup = "Reparacion por Garantia";
    $coment_e = "Reparacion Anterior ID: " . $idRepaAnt;
    $medio_pago= "No corresponde";
    $nro_comprobante="-";
    $monto_fijo= 0;
    $comentario_cobro= "Ingresa por garantia, no debe abonar antes de revisarlo.";

    $reparacion = new Electro($base);

    $reparacion->setIdCli($id_cli);
    $reparacion->setIdElectro($idElectro);
    $reparacion->setEdComentario($coment_e);
    $reparacion->setDescripcion($desc);
    $reparacion->setIdTecnico($tecnico);
    $reparacion->setDescReparacion($idRepaAnt);
    $reparacion->setIdEmpAtencion($idEmp);

    $reparacion->setMedioPagoIni($medio_pago);
    $reparacion->setNroComproIni($nro_comprobante);
    $reparacion->setMontoFijoIni($monto_fijo);
    $reparacion->setComentariosCobro($comentario_cobro);
    $reparacion->setEstadoReparacion($estado_repa);
    $reparacion->setEstadoPresu($estado_presup);
    


    $resultado = $reparacion->addNuevaRepa($idRepaAnt);
    if ($resultado === true) {
        // Incluir la clase de notificaciones
            include_once '../notificaciones/notificaciones_class.php';

            $notificacion = new Notificacion($base);
            
            // Crear el mensaje y el link para la notificación
            $mensaje = "Ha ingresado una nueva Reparación por Garantía que te han asignado.";
            $link = "/php/proyectoElReparador/electrodomesticos/inicioElectro.php";
            
            // Crear la notificación para el técnico asignado ($tecnico)
            $notificacion->crearNoti($tecnico, $mensaje, $link);

            echo "<script>alert('Nueva Revisión por Garantía agregada con éxito.'); window.location.href = 'inicioElectro.php';</script>";
    }else{
        //echo $resultado;
        var_dump($resultado);
        exit;
    }
}
?>