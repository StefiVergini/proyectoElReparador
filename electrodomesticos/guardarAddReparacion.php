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
    $desc = $_POST['desc'];
    $coment_e = $_POST['coment_e'];
    $tecnico = $_POST['tecnicos']; 
    $idEmp= $_SESSION['id'];

    $medio_pago = $_POST['medio_pago'];
    $nro_comprobante = $_POST['nro_comprobante'];
    $monto_fijo = $_POST['monto_fijo'];
    $comentario_cobro =$_POST['comentario'];
    $estado_repa = 0;
    $estado_presup = "Presupuesto a Enviar";
    
    $reparacion = new Electro($base);

    $reparacion->setIdCli($id_cli);
    $reparacion->setIdElectro($idElectro);
    $reparacion->setDescripcion($desc);
    $reparacion->setEdComentario($coment_e);
    $reparacion->setIdTecnico($tecnico);
    $reparacion->setIdEmpAtencion($idEmp);   
    $reparacion->setMedioPagoIni($medio_pago);
    $reparacion->setNroComproIni($nro_comprobante);
    $reparacion->setMontoFijoIni($monto_fijo);
    $reparacion->setComentariosCobro($comentario_cobro);
    $reparacion->setEstadoReparacion($estado_repa);
    $reparacion->setEstadoPresu($estado_presup);
    


    $resultado = $reparacion->addNuevaRepa();
    if ($resultado === true) {
        // Incluir la clase de notificaciones
            include_once '../notificaciones/notificaciones_class.php';
            // Instanciarla (asumiendo que su constructor requiere el objeto PDO)
            $notificacion = new Notificacion($base);
            
            // Crear el mensaje y el link para la notificación
            $mensaje = "Ha ingresado una nueva Reparación que te han asignado.";
            $link = "/php/proyectoElReparador/electrodomesticos/inicioElectro.php";
            
            // Crear la notificación para el técnico asignado ($tecnico)
            $notificacion->crearNoti($tecnico, $mensaje, $link);

            echo "<script>alert('Nueva reparacion agregada con éxito.'); window.location.href = 'inicioElectro.php';</script>";
    }else{
        //echo $resultado;
        var_dump($resultado);
        exit;
    }
}
?>