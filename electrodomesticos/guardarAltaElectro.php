<?php
include("../conexionPDO.php");
include("electro_class.php");

session_start();
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
//exit;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cli = $_POST['n_id'];
    $tipo_electro = $_POST['tipo'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo']; 
    $num_serie = $_POST['num_serie']; 
    $desc = $_POST['desc']; 
    $tecnico = $_POST['tecnicos']; 
    $idEmp= $_SESSION['id'];
    $medio_pago = $_POST['medio_pago'];
    $nro_comprobante = $_POST['nro_comprobante'];
    $monto_fijo = $_POST['monto_fijo'];
    $comentario_cobro =$_POST['comentario'];
    $estado_repa = 0;
    $estado_presup = "Presupuesto a Enviar";
    
    $reparacion = new Electro($base);

    $reparacion->setMarca($marca);
    $reparacion->setModelo($modelo);
    $reparacion->setNumSerie($num_serie);
    $reparacion->setDescripcion($desc);
    $reparacion->setIdCli($id_cli);
    $reparacion->setTipoElectro($tipo_electro);
    $reparacion->setIdTecnico($tecnico);
    $reparacion->setEstadoReparacion($estado_repa);
    $reparacion->setIdEmpAtencion($idEmp);
    $reparacion->setMedioPagoIni($medio_pago);
    $reparacion->setNroComproIni($nro_comprobante);
    $reparacion->setMontoFijoIni($monto_fijo);
    $reparacion->setComentariosCobro($comentario_cobro);
    $reparacion->setEstadoPresu($estado_presup);


    $resultado = $reparacion->altaElectro();
    if ($resultado === true) {
        // Incluir la clase de notificaciones
            include_once '../notificaciones/notificaciones_class.php';
            // Instanciarla (asumiendo que su constructor requiere el objeto PDO)
            $notificacion = new Notificacion($base);
            
            // Crear el mensaje y el link para la notificación
            $mensaje = "Ha ingresado un nuevo Electrodoméstico que te han asignado.";
            $link = "/php/proyectoElReparador/electrodomesticos/inicioElectro.php";
            
            // Crear la notificación para el técnico asignado ($tecnico)
            $notificacion->crearNoti($tecnico, $mensaje, $link);

        echo "<script>alert('Reparacion guardada con éxito.'); window.location.href = 'inicioElectro.php';</script>";
    }else{
        //echo $resultado;
        var_dump($resultado);
        exit;
    }
}
?>