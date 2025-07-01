<?php
class Electro {

    //atributos del electrodomestico
    private $id_electro;
    private $marca;
    private $modelo;
    private $num_serie;
    private $descripcion;
    private $id_cli;
    private $nom_cli;
    private $ape_cli;
    private $email_cli;
    private $tipo_electro;
    // atributos del tipo de electro
    private $nom_tipo;
    // atributos de la reparacion
    private $id_reparacion;
    private $id_tecnico;
    private $nom_tecnico;
    private $ape_tecnico;
    private $fecha_inicio;
    private $fecha_fin_estimada;
    private $fecha_fin;
    private $fecha_de_retiro;
    private $fecha_fin_garantia;
    private $desc_reparacion;
    private $estado_reparacion;
    // atributos del presupuesto
    private $id_emp_atencion;
    private $nom_emp_atencion;
    private $ape_emp_atencion;
    private $presupuesto;
    private $idemp_presu;
    private $nom_emp_presu;
    private $ape_emp_presu; 
    private $fecha_ing_electro;
    private $fecha_envio_presup;
    private $fecha_confirm_reparacion;
    private $confirma_presupuesto; // 0 no confirma - 1 confirma
    private $estado_presup;
    private $observaciones;
    
    //atributos de cobros
    private $id_cobro;
    private $fecha_cobro_ini;
    private $monto_fijo_cobro_ini;
    private $nro_compro_inicial;
    private $medio_pago_inicial;
    private $fecha_cobro_final;
    private $monto_final_repa;
    private $medio_pago_final;
    private $nro_compro_final;
    private $comentarios;

    // atributos db
    private $db;

    //constructor
    public function __construct($db) {
        $this->db = $db; // conexión PDO
    }

    public function leerTipoElectro(){
        try{
            $query= "SELECT idtipo_electro, nom_tipo FROM tipo_electro ORDER BY nom_tipo";
            $ps = $this->db->prepare($query);
            $ps->execute();
            $tipos = [];
            
            // Recorrer resultados y crear objetos de la clase
            while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $tipo = new Electro($this->db);
                $tipo->setTipoElectro($fila['idtipo_electro']);
                $tipo->setNomTipo($fila['nom_tipo']);
                $tipos[] = $tipo; // Agrega el objeto al array
            }
    
            return $tipos;
    
        } catch (Exception $e) {
            echo "Error al obtener los tipos de Electrodomesticos: " . $e->getMessage();
            return [];
        }
    }
    public function filtrarHistorialReparaciones($desde, $hasta, $cliente) {
        try {
                    // 1) Base de la consulta (sin WHERE ni ORDER)
            $sql = "SELECT 
                    r.id_reparacion, r.idelectrodomesticos, r.id_tecnico, 
                    r.fecha_inicio, r.fecha_fin_estimada, r.fecha_finalizacion, 
                    r.fecha_retiro_electro, r.fecha_finaliza_garantia, r.descripcion_re, r.estado_reparacion, 

                    e.marca, e.modelo, e.num_serie, e.descripcion, e.idclientes, e.tipo_electro, 
                    t.nom_tipo, 

                    a.idemp_atencion AS id_atencion, a.idemp_presup AS id_presupuestador, a.presupuesto, a.fecha_ing_electro, a.fecha_env_presup, a.fecha_confirma_re, a.estado_presup,
                    a.confirm_presup, a.observaciones, 

                    c.nom_cliente, c.ape_cliente, c.email_cliente, 

                    tecnico.nom_empleado AS nom_tecnico, tecnico.ape_empleado AS ape_tecnico, 
                    presupuestador.nom_empleado AS nom_presupuestador, presupuestador.ape_empleado AS ape_presupuestador,
                    atencion.nom_empleado AS nom_atencion, atencion.ape_empleado AS ape_atencion,

                    co.id_cobro, co.fecha_cobro_inicial, co.arancel_fijo_cobrado, co.medio_pago_inicial, co.nro_comprobante_inicial, co.fecha_cobro_final, co.monto_final_repa, co.medio_pago_final, co.nro_comprobante_final, co.observacion 
                FROM reparaciones AS r
                INNER JOIN electrodomesticos AS e      ON r.idelectrodomesticos = e.idelectrodomesticos
                INNER JOIN empleados AS tecnico       ON r.id_tecnico          = tecnico.idempleados 
                INNER JOIN tipo_electro AS t          ON t.idtipo_electro      = e.tipo_electro
                INNER JOIN atencion_presupuesto AS a  ON a.id_reparacion       = r.id_reparacion
                LEFT JOIN empleados AS presupuestador ON presupuestador.idempleados = a.idemp_presup
                INNER JOIN empleados AS atencion      ON atencion.idempleados  = a.idemp_atencion
                INNER JOIN clientes AS c              ON c.idclientes          = e.idclientes
                INNER JOIN cobros AS co               ON co.id_reparacion      = r.id_reparacion";

            // 2) Construir dinámicamente el WHERE
            $conds  = ["(a.estado_presup = 'Reparacion Cobrada' OR a.estado_presup = 'Presupuesto Rechazado')"];
            $params = [];

            // Si recibimos un rango de fechas válido, lo agregamos
            if (!empty($desde) && !empty($hasta)) {
                $conds[]          = "a.fecha_ing_electro BETWEEN :desde AND :hasta";
                $params[':desde'] = $desde;
                $params[':hasta'] = $hasta;
            }

            // **Filtro por cliente:**
            if (!empty($cliente)) {
                $conds[] = "(c.nom_cliente LIKE :cliente OR c.ape_cliente LIKE :cliente)";
                $params[':cliente'] = "%" . $cliente . "%"; // Agregar comodines para búsqueda parcial
            }
            // Unir condiciones
            $sql .= " WHERE " . implode(" AND ", $conds);

            // 3) Agregar orden
            $sql .= " ORDER BY r.fecha_inicio DESC";

            // 4) Preparar y ejecutar
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->execute();

            // 5) Mapear a objetos
            $reparaciones = [];

            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reparacion = new Electro($this->db);
                $reparacion->setIdReparacion($fila['id_reparacion']);
                $reparacion->setIdElectro($fila['idelectrodomesticos']);
                $reparacion->setIdTecnico($fila['id_tecnico']);
                $reparacion->setFechaInicio($fila['fecha_inicio']);
                $reparacion->setFechaFinEst($fila['fecha_fin_estimada']);
                $reparacion->setFechaFin($fila['fecha_finalizacion']);
                $reparacion->setFechaDeRetiro($fila['fecha_retiro_electro']);
                $reparacion->setFechaFinGarantia($fila['fecha_finaliza_garantia']);
                $reparacion->setDescReparacion($fila['descripcion_re']);
                $reparacion->setEstadoReparacion($fila['estado_reparacion']);
    
                $reparacion->setMarca($fila['marca']);
                $reparacion->setModelo($fila['modelo']);
                $reparacion->setNumSerie($fila['num_serie']);
                $reparacion->setDescripcion($fila['descripcion']);
                $reparacion->setIdCli($fila['idclientes']);
                $reparacion->setTipoElectro($fila['tipo_electro']);
                $reparacion->setNomTipo($fila['nom_tipo']);
    
                $reparacion->setIdEmpAtencion($fila['id_atencion']);
                $reparacion->setIdEmpPresu($fila['id_presupuestador']);
                $reparacion->setPresupuesto($fila['presupuesto']);
                $reparacion->setFechaIngElectro($fila['fecha_ing_electro']);
                $reparacion->setFechaEnvioPresup($fila['fecha_env_presup']);
                $reparacion->setFechaConfirmReparacion($fila['fecha_confirma_re']);
                $reparacion->setEstadoPresu($fila['estado_presup']);
                $reparacion->setConfirmaPresupuesto($fila['confirm_presup']);
                $reparacion->setObservaciones($fila['observaciones']);
    
                $reparacion->setNomCli($fila['nom_cliente']);
                $reparacion->setApeCliente($fila['ape_cliente']);
                $reparacion->setEmailCliente($fila['email_cliente']);
    
                $reparacion->setNomTecnico($fila['nom_tecnico']);
                $reparacion->setApeTecnico($fila['ape_tecnico']);
                $reparacion->setNomEmpAtencion($fila['nom_atencion']);
                $reparacion->setApeEmpAtencion($fila['ape_atencion']);
                $reparacion->setNomEmpPresu($fila['nom_presupuestador']);
                $reparacion->setApeEmpPresu($fila['ape_presupuestador']);
                $reparacion->setIdCobro($fila['id_cobro']);
                $reparacion->setFechaCobroIni($fila['fecha_cobro_inicial']);
                $reparacion->setMontoFijoIni($fila['arancel_fijo_cobrado']);
                $reparacion->setMedioPagoIni($fila['medio_pago_inicial']);
                $reparacion->setNroComproIni($fila['nro_comprobante_inicial']);
                $reparacion->setFechaCobroFin($fila['fecha_cobro_final']);
                $reparacion->setMontoFinRepa($fila['monto_final_repa']);
                $reparacion->setMedioPagoFin($fila['medio_pago_final']);
                $reparacion->setNroComproFin($fila['nro_comprobante_final']);
                $reparacion->setComentariosCobro($fila['observacion']);
    
                $reparaciones[] = $reparacion;
            }

            return $reparaciones;
        } catch (Exception $e) {
            echo "Error al filtrar el historial: " . $e->getMessage();
            return [];
        }
    }

    public function leerReparaciones() {
       
        try {
            $query = "SELECT 
                r.id_reparacion, r.idelectrodomesticos, r.id_tecnico, 
                r.fecha_inicio, r.fecha_fin_estimada, r.fecha_finalizacion, 
                r.fecha_retiro_electro, r.fecha_finaliza_garantia, r.descripcion_re, r.estado_reparacion, 

                e.marca, e.modelo, e.num_serie, e.descripcion, e.idclientes, e.tipo_electro, 
                t.nom_tipo, 

                a.idemp_atencion AS id_atencion, a.idemp_presup AS id_presupuestador, a.presupuesto, a.fecha_ing_electro, a.fecha_env_presup, a.fecha_confirma_re, a.estado_presup,
                a.confirm_presup, a.observaciones, 

                c.nom_cliente, c.ape_cliente, c.email_cliente, 

                tecnico.nom_empleado AS nom_tecnico, tecnico.ape_empleado AS ape_tecnico, 
                presupuestador.nom_empleado AS nom_presupuestador, presupuestador.ape_empleado AS ape_presupuestador,
                atencion.nom_empleado AS nom_atencion, atencion.ape_empleado AS ape_atencion,

                co.id_cobro, co.fecha_cobro_inicial, co.arancel_fijo_cobrado, co.medio_pago_inicial, co.nro_comprobante_inicial, co.fecha_cobro_final, co.monto_final_repa, co.medio_pago_final, co.nro_comprobante_final, co.observacion 
            FROM reparaciones AS r
            INNER JOIN electrodomesticos AS e ON r.idelectrodomesticos = e.idelectrodomesticos
            INNER JOIN empleados AS tecnico ON r.id_tecnico = tecnico.idempleados 
            INNER JOIN tipo_electro AS t ON t.idtipo_electro = e.tipo_electro
            INNER JOIN atencion_presupuesto AS a ON a.id_reparacion = r.id_reparacion
            LEFT JOIN empleados AS presupuestador ON presupuestador.idempleados = a.idemp_presup
            INNER JOIN empleados AS atencion ON atencion.idempleados = a.idemp_atencion
            INNER JOIN clientes AS c ON c.idclientes = e.idclientes
            INNER JOIN cobros AS co ON co.id_reparacion = r.id_reparacion";

            // Si el rol es de tecnico = 1, 7 o 8 se muestran solo las reparaciones asignadas a ese tecnico
            if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 7, 8])) {
                $query .= " WHERE r.id_tecnico = :id_tecnico";
            }
            
            
            $query .= " ORDER BY a.fecha_ing_electro";

            // Preparar la consulta
            $ps = $this->db->prepare($query);
            if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 7, 8])) {
                $ps->bindParam(':id_tecnico', $_SESSION['id'], PDO::PARAM_INT);
            }
            $ps->execute();

            $reparaciones = [];
    
            while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $reparacion = new Electro($this->db);
                //var_dump($fila); 
                $reparacion->setIdReparacion($fila['id_reparacion']);
                $reparacion->setIdElectro($fila['idelectrodomesticos']);
                $reparacion->setIdTecnico($fila['id_tecnico']);
                $reparacion->setFechaInicio($fila['fecha_inicio']);
                $reparacion->setFechaFinEst($fila['fecha_fin_estimada']);
                $reparacion->setFechaFin($fila['fecha_finalizacion']);
                $reparacion->setFechaDeRetiro($fila['fecha_retiro_electro']);
                $reparacion->setFechaFinGarantia($fila['fecha_finaliza_garantia']);
                $reparacion->setDescReparacion($fila['descripcion_re']);
                $reparacion->setEstadoReparacion($fila['estado_reparacion']);
    
                $reparacion->setMarca($fila['marca']);
                $reparacion->setModelo($fila['modelo']);
                $reparacion->setNumSerie($fila['num_serie']);
                $reparacion->setDescripcion($fila['descripcion']);
                $reparacion->setIdCli($fila['idclientes']);
                $reparacion->setTipoElectro($fila['tipo_electro']);
                $reparacion->setNomTipo($fila['nom_tipo']);
    
                $reparacion->setIdEmpAtencion($fila['id_atencion']);
                $reparacion->setIdEmpPresu($fila['id_presupuestador']);
                $reparacion->setPresupuesto($fila['presupuesto']);
                $reparacion->setFechaIngElectro($fila['fecha_ing_electro']);
                $reparacion->setFechaEnvioPresup($fila['fecha_env_presup']);
                $reparacion->setFechaConfirmReparacion($fila['fecha_confirma_re']);
                $reparacion->setEstadoPresu($fila['estado_presup']);
                $reparacion->setConfirmaPresupuesto($fila['confirm_presup']);
                $reparacion->setObservaciones($fila['observaciones']);
    
                $reparacion->setNomCli($fila['nom_cliente']);
                $reparacion->setApeCliente($fila['ape_cliente']);
                $reparacion->setEmailCliente($fila['email_cliente']);
    
                $reparacion->setNomTecnico($fila['nom_tecnico']);
                $reparacion->setApeTecnico($fila['ape_tecnico']);
                $reparacion->setNomEmpAtencion($fila['nom_atencion']);
                $reparacion->setApeEmpAtencion($fila['ape_atencion']);
                $reparacion->setNomEmpPresu($fila['nom_presupuestador']);
                $reparacion->setApeEmpPresu($fila['ape_presupuestador']);
                $reparacion->setIdCobro($fila['id_cobro']);
                $reparacion->setFechaCobroIni($fila['fecha_cobro_inicial']);
                $reparacion->setMontoFijoIni($fila['arancel_fijo_cobrado']);
                $reparacion->setMedioPagoIni($fila['medio_pago_inicial']);
                $reparacion->setNroComproIni($fila['nro_comprobante_inicial']);
                $reparacion->setFechaCobroFin($fila['fecha_cobro_final']);
                $reparacion->setMontoFinRepa($fila['monto_final_repa']);
                $reparacion->setMedioPagoFin($fila['medio_pago_final']);
                $reparacion->setNroComproFin($fila['nro_comprobante_final']);
                $reparacion->setComentariosCobro($fila['observacion']);
    
                $reparaciones[] = $reparacion;
            }
            //echo "<pre>";
            //var_dump($reparaciones); // si usás PDO::FETCH_CLASS o similar
            //echo "</pre>";  
            return $reparaciones;
        } catch (Exception $e) {
            echo "Error al obtener las Reparaciones: " . $e->getMessage();
            return [];
        }
        
    }
    
    public function leerPresupuestos($idRe) {
        try {
            $query = "SELECT 
                        p.idemp_atencion AS id_atencion, p.idemp_presup AS id_presupuestador, p.presupuesto, p.fecha_ing_electro, p.fecha_env_presup, p.fecha_confirma_re, p.confirm_presup, p.estado_presup, p.observaciones, 
                        atencion.nom_empleado AS nom_atencion, atencion.ape_empleado AS ape_atencion,
                        presupuestador.nom_empleado AS nom_presupuestador, presupuestador.ape_empleado AS ape_presupuestador
                    FROM atencion_presupuesto AS p
                    INNER JOIN empleados ON p.idemp_atencion = atencion.idempleados
                    INNER JOIN empleados ON p.idemp_presup = presupuestador.idempleados 
                    WHERE p.id_reparacion = :id";
    
            $ps = $this->db->prepare($query);
            $ps->bindParam(':id', $idRe, PDO::PARAM_INT);
            $ps->execute();
            $presupuestos = [];
    
            while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $presupuesto = new Electro($this->db);
                $presupuesto->setIdEmpAtencion($fila['id_atencion']);
                $presupuesto->setIdEmpPresu($fila['id_presupuestador']);
                $presupuesto->setPresupuesto($fila['presupuesto']);
                $presupuesto->setFechaIngElectro($fila['fecha_ing_electro']);
                $presupuesto->setFechaEnvioPresup($fila['fecha_env_presup']);
                $presupuesto->setFechaConfirmReparacion($fila['fecha_confirma_re']);
                $presupuesto->setConfirmaPresupuesto($fila['confirm_presup']);
                $presupuesto->setEstadoPresu($fila['estado_presup']);
                $presupuesto->setObservaciones($fila['observaciones']);
                $presupuesto->setNomEmpAtencion($fila['nom_atencion']);
                $presupuesto->setApeEmpAtencion($fila['ape_atencion']);
                $presupuesto->setNomEmpPresu($fila['nom_presupuestador']);
                $presupuesto->setApeEmpPresu($fila['ape_presupuestador']);

        
                $presupuestos[] = $presupuesto;
            }
    
            return $presupuestos;
        } catch (Exception $e) {
            echo "Error al obtener los Presupuestos: " . $e->getMessage();
            return [];
        }
    }

    public function enviarPresupuesto($idRe){
        try{
            $this->db->beginTransaction();
            $queryAtencion = "UPDATE atencion_presupuesto SET presupuesto = :presu, idemp_presup = :idemp, observaciones = :desc_presu, estado_presup = :estado, fecha_env_presup = NOW() WHERE id_reparacion = :id_repa";

            $psAtencion = $this->db->prepare($queryAtencion);

            $presu = $this->getPresupuesto();
            $idemp= $this->getIdEmpPresu();
            $descPresu = $this->getObservaciones();
            $estado_presup = $this->getEstadoPresu();

            $psAtencion->bindParam(':id_repa', $idRe, PDO::PARAM_INT);
            $psAtencion->bindParam(':presu', $presu);
            $psAtencion->bindParam(':idemp', $idemp);
            $psAtencion->bindParam(':desc_presu', $descPresu, PDO::PARAM_STR);
            $psAtencion->bindParam(':estado', $estado_presup);

            $psAtencion->execute();

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            echo "Error: " . $e->getMessage(); // Agregado para depuración
            error_log("Error al guardar y enviar presupuesto: " . $e->getMessage());
            return false;
        }
       
    }
    public function confirmarPresupuesto($idRe){
        try{
            $this->db->beginTransaction();
            $queryAtencion = "UPDATE atencion_presupuesto SET fecha_confirma_re = NOW(), confirm_presup = :confirm_presup, estado_presup = :estado WHERE id_reparacion = :id_repa";

            $psAtencion = $this->db->prepare($queryAtencion);

            $confir_presu = $this->getConfirmaPresupuesto();
            $estado_pre = $this->getEstadoPresu();

            $psAtencion->bindParam(':id_repa', $idRe, PDO::PARAM_INT);
            $psAtencion->bindParam(':confirm_presup', $confir_presu);
            $psAtencion->bindParam(':estado', $estado_pre);

            $psAtencion->execute();

            $queryRepa = "UPDATE reparaciones SET fecha_inicio = NOW(), fecha_fin_estimada = :fecha_fin_est, estado_reparacion = :estado WHERE id_reparacion = :id_repa";

            $psRepa = $this->db->prepare($queryRepa);

            $fecha_fin_estimada = $this->getFechaFinEst();
            $estado_re = $this->getEstadoReparacion();

            $psRepa->bindParam(':id_repa', $idRe, PDO::PARAM_INT);
            $psRepa->bindParam(':fecha_fin_est', $fecha_fin_estimada);
            $psRepa->bindParam(':estado', $estado_re);

            $psRepa->execute();

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            echo "Error: " . $e->getMessage(); // Agregado para depuración
            error_log("Error al confirmar la reparación: " . $e->getMessage());
            return false;
        }
       
    }

    public function rechazarPresupuesto($idRe, $comentCobro){
        try{
            $this->db->beginTransaction();
            $queryAtencion = "UPDATE atencion_presupuesto SET fecha_confirma_re = NOW(), estado_presup = :estado WHERE id_reparacion = :id_repa";

            $psAtencion = $this->db->prepare($queryAtencion);

            $confir_presu = $this->getConfirmaPresupuesto();
            $estado_pre = $this->getEstadoPresu();

            $psAtencion->bindParam(':id_repa', $idRe, PDO::PARAM_INT);
            $psAtencion->bindParam(':estado', $estado_pre);

            $psAtencion->execute();

            $queryRepa = "UPDATE reparaciones SET fecha_finalizacion = NOW() WHERE id_reparacion = :id_repa";

            $psRepa = $this->db->prepare($queryRepa);

            $psRepa->bindParam(':id_repa', $idRe, PDO::PARAM_INT);

            $psRepa->execute();

            $queryCobros = "UPDATE cobros SET monto_final_repa = :monto_final, medio_pago_final = :medio_pago, nro_comprobante_final = :nro_compro_final, observacion = :comentario WHERE id_reparacion = :id_repa";

            $psCobros = $this->db->prepare($queryCobros);

            $monto_final_repa = $this->getMontoFinRepa();
            $medio_pago_final = $this->getMedioPagoFin();
            $nro_compro_final = $this->getNroComproFin();

            $psCobros->bindParam(':id_repa', $idRe, PDO::PARAM_INT);
            $psCobros->bindParam(':monto_final', $monto_final_repa);
            $psCobros->bindParam(':medio_pago', $medio_pago_final);
            $psCobros->bindParam(':nro_compro_final', $nro_compro_final);
            $psCobros->bindParam(':comentario', $comentCobro);

            $psCobros->execute();

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            echo "Error: " . $e->getMessage(); // Agregado para depuración
            error_log("Error al rechazar la reparación: " . $e->getMessage());
            return false;
        }
       
    }
    public function reparacionFinalizada($idRe){
        try{
            $this->db->beginTransaction();
            $queryAtencion = "UPDATE atencion_presupuesto SET estado_presup = :estado WHERE id_reparacion = :id_repa";

            $psAtencion = $this->db->prepare($queryAtencion);

            $estado_pre = $this->getEstadoPresu();
            $psAtencion->bindParam(':id_repa', $idRe, PDO::PARAM_INT); 
            $psAtencion->bindParam(':estado', $estado_pre);

            $psAtencion->execute();

            $queryRepa = "UPDATE reparaciones SET fecha_finalizacion = NOW(), estado_reparacion = :estado_repa WHERE id_reparacion = :id_repa";

            $psRepa = $this->db->prepare($queryRepa);
            $estado_reparacion = $this->getEstadoReparacion();

            $psRepa->bindParam(':id_repa', $idRe, PDO::PARAM_INT);
            $psRepa->bindParam(':estado_repa', $estado_reparacion);

            $psRepa->execute();

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            echo "Error: " . $e->getMessage(); // Agregado para depuración
            error_log("Error al Finalizar la reparación: " . $e->getMessage());
            return false;
        }
    }

    public function reparacionCobrada($idRe){
        try{
            $this->db->beginTransaction();
            $queryAtencion = "UPDATE atencion_presupuesto SET estado_presup = :estado WHERE id_reparacion = :id_repa";

            $psAtencion = $this->db->prepare($queryAtencion);

            $estado_pre = $this->getEstadoPresu();
            $psAtencion->bindParam(':id_repa', $idRe, PDO::PARAM_INT); 
            $psAtencion->bindParam(':estado', $estado_pre);

            $psAtencion->execute();

            $queryRepa = "UPDATE reparaciones SET fecha_retiro_electro = NOW(), fecha_finaliza_garantia = DATE_ADD(NOW(), INTERVAL 3 MONTH) WHERE id_reparacion = :id_repa";

            $psRepa = $this->db->prepare($queryRepa);

            $psRepa->bindParam(':id_repa', $idRe, PDO::PARAM_INT);

            $psRepa->execute();

            $queryCobro= "UPDATE cobros SET fecha_cobro_final = NOW(), monto_final_repa = :monto_fin, nro_comprobante_final = :nro_compro_fin, medio_pago_final = :medio_fin, observacion = :coment WHERE id_reparacion = :id_repa";

            $psCobro = $this->db->prepare($queryCobro);
            $medioPago = $this->getMedioPagoFin();
            $comentarios =$this->getComentariosCobro();
            $nroCompro = $this->getNroComproFin();
            $montoFin =$this->getMontoFinRepa();

            $psCobro->bindParam(':id_repa', $idRe, PDO::PARAM_INT);
            $psCobro->bindParam(':monto_fin', $montoFin);
            $psCobro->bindParam(':nro_compro_fin', $nroCompro);
            $psCobro->bindParam(':medio_fin', $medioPago);
            $psCobro->bindParam(':coment', $comentarios);

            $psCobro->execute();

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            echo "Error: " . $e->getMessage(); // Agregado para depuración
            error_log("Error al Finalizar la reparación: " . $e->getMessage());
            return false;
        }
    }
    public function altaElectro() {
        try {
            $this->db->beginTransaction();
    
            // Insertar electrodoméstico
            $queryElectro = "INSERT INTO electrodomesticos (marca, modelo, num_serie, descripcion, idclientes, tipo_electro) 
                             VALUES (:marca, :modelo, :num_serie, :descripcion, :id_cli, :tipo_e)";
    
            $psElectro = $this->db->prepare($queryElectro);
    
            // Guardamos los valores en variables
            $marca = $this->getMarca();
            $modelo = $this->getModelo();
            $num_serie = $this->getNumSerie();
            $descripcion = $this->getDescripcion();
            $id_cli = $this->getIdCli();
            $tipo_e = $this->getTipoElectro();
    
            // Pasamos las variables a bindParam()
            $psElectro->bindParam(':marca', $marca, PDO::PARAM_STR);
            $psElectro->bindParam(':modelo', $modelo, PDO::PARAM_STR);
            $psElectro->bindParam(':num_serie', $num_serie, PDO::PARAM_STR);
            $psElectro->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $psElectro->bindParam(':id_cli', $id_cli, PDO::PARAM_INT);
            $psElectro->bindParam(':tipo_e', $tipo_e, PDO::PARAM_STR);
            
            $psElectro->execute();
    
            $id_electro = $this->db->lastInsertId();
    
            // Insertar reparación
            $queryRepa = "INSERT INTO reparaciones (idelectrodomesticos, id_tecnico, estado_reparacion) 
                          VALUES (:id_electro, :id_tecnico, :estado_re)";
    
            $psRepa = $this->db->prepare($queryRepa);
    
            // Guardamos los valores en variables
            $id_tecnico = $this->getIdTecnico();
            $estado_re = $this->getEstadoReparacion();
    
            $psRepa->bindParam(':id_electro', $id_electro, PDO::PARAM_INT);
            $psRepa->bindParam(':id_tecnico', $id_tecnico, PDO::PARAM_INT);
            $psRepa->bindParam(':estado_re', $estado_re, PDO::PARAM_STR);
            $psRepa->execute();
    
            $id_repa = $this->db->lastInsertId();
    
            // Insertar en atención_presupuesto
            $queryPresu = "INSERT INTO atencion_presupuesto (id_reparacion, idemp_atencion,fecha_ing_electro,estado_presup) 
                           VALUES (:id_repa, :id_emp, NOW(), :estado_presup)";
    
            $psPresu = $this->db->prepare($queryPresu);
    
            // Guardamos el valor en una variable
            $id_emp = $this->getIdEmpAtencion();
            $estado_presup = $this->getEstadoPresu();
    
            $psPresu->bindParam(':id_repa', $id_repa, PDO::PARAM_INT);
            $psPresu->bindParam(':id_emp', $id_emp, PDO::PARAM_INT);
            $psPresu->bindParam(':estado_presup', $estado_presup);
            $psPresu->execute();

            // Insertar en cobros
            $queryCobros = "INSERT INTO cobros (id_reparacion, fecha_cobro_inicial,arancel_fijo_cobrado, medio_pago_inicial, nro_comprobante_inicial, observacion) 
                           VALUES (:id_repa, NOW(), :arancel_fijo, :medio_pago, :nro_compro_ini, :comentarios)";
    
            $psCobros = $this->db->prepare($queryCobros);
    
            // Guardamos el valor en una variable
            $monto_fijo_cobro_ini = $this->getMontoFijoIni();
            $medio_pago_inicial = $this->getMedioPagoIni();
            $nro_compro_inicial = $this->getNroComproIni();
            $comentarios = $this->getComentariosCobro();
    
            $psCobros->bindParam(':id_repa', $id_repa, PDO::PARAM_INT);
            $psCobros->bindParam(':arancel_fijo', $monto_fijo_cobro_ini);
            $psCobros->bindParam(':medio_pago', $medio_pago_inicial);
            $psCobros->bindParam(':nro_compro_ini', $nro_compro_inicial);
            $psCobros->bindParam(':comentarios', $comentarios);
            $psCobros->execute();
    
            $this->db->commit();
            return true;
    
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error al dar de alta el electrodoméstico, cobro e inicio del presupuesto: " . $e->getMessage());
            return false;
        }
    }

    // Getters y Setters
    public function getIdElectro() { return $this->id_electro; }
    public function setIdElectro($id_electro) { $this->id_electro = $id_electro; }

    public function getMarca() { return $this->marca; }
    public function setMarca($marca) { $this->marca = $marca; }

    public function getModelo() { return $this->modelo; }
    public function setModelo($modelo) { $this->modelo = $modelo; }

    public function getNumSerie() { return $this->num_serie; }
    public function setNumSerie($num_serie) { $this->num_serie = $num_serie; }

    public function getDescripcion() { return $this->descripcion; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }

    public function getIdCli() { return $this->id_cli; }
    public function setIdCli($id_cli) { $this->id_cli = $id_cli; }

    public function getNomCli() { return $this->nom_cli; }
    public function setNomCli($nom_cli) { $this->nom_cli = $nom_cli; }

    public function getApeCliente() { return $this->ape_cli; }
    public function setApeCliente($ape_cli) { $this->ape_cli = $ape_cli; }

    public function getEmailCliente() { return $this->email_cli; }
    public function setEmailCliente($email_cli) { $this->email_cli = $email_cli; }

    public function getTipoElectro() { return $this->tipo_electro; }
    public function setTipoElectro($tipo_electro) { $this->tipo_electro = $tipo_electro; }

    public function getNomTipo() { return $this->nom_tipo; }
    public function setNomTipo($nom_tipo) { $this->nom_tipo = $nom_tipo; }

    public function getIdReparacion() { return $this->id_reparacion; }
    public function setIdReparacion($id_reparacion) { $this->id_reparacion = $id_reparacion; }

    public function getIdTecnico() { return $this->id_tecnico; }
    public function setIdTecnico($id_tecnico) { $this->id_tecnico = $id_tecnico; }

    public function getNomTecnico() { return $this->nom_tecnico; }
    public function setNomTecnico($nom_tecnico) { $this->nom_tecnico = $nom_tecnico; }

    public function getApeTecnico() { return $this->ape_tecnico; }
    public function setApeTecnico($ape_tecnico) { $this->ape_tecnico = $ape_tecnico; }

    public function getFechaInicio() { return $this->fecha_inicio; }
    public function setFechaInicio($fecha_inicio) { $this->fecha_inicio = $fecha_inicio; }

    public function getFechaFinEst() { return $this->fecha_fin_estimada; }
    public function setFechaFinEst($fecha_fin_estimada) { $this->fecha_fin_estimada = $fecha_fin_estimada; }

    public function getFechaFin() { return $this->fecha_fin; }
    public function setFechaFin($fecha_fin) { $this->fecha_fin = $fecha_fin; }

    public function getFechaDeRetiro() { return $this->fecha_de_retiro; }
    public function setFechaDeRetiro($fecha_de_retiro) { $this->fecha_de_retiro = $fecha_de_retiro; }

    public function getFechaFinGarantia() { return $this->fecha_fin_garantia; }
    public function setFechaFinGarantia($fecha_fin_garantia) { $this->fecha_fin_garantia = $fecha_fin_garantia; }

    public function getDescReparacion() { return $this->desc_reparacion; }
    public function setDescReparacion($desc_reparacion) { $this->desc_reparacion = $desc_reparacion; }

    public function getEstadoReparacion() { return $this->estado_reparacion; }
    public function setEstadoReparacion($estado_reparacion) { $this->estado_reparacion = $estado_reparacion; }

    public function getIdEmpAtencion() { return $this->id_emp_atencion; }
    public function setIdEmpAtencion($id_emp_atencion) { $this->id_emp_atencion = $id_emp_atencion; }

    public function getNomEmpAtencion() { return $this->nom_emp_atencion; }
    public function setNomEmpAtencion($nom_emp_atencion) { $this->nom_emp_atencion = $nom_emp_atencion; }

    public function getApeEmpAtencion() { return $this->ape_emp_atencion; }
    public function setApeEmpAtencion($ape_emp_atencion) { $this->ape_emp_atencion = $ape_emp_atencion; }

    public function getIdEmpPresu() { return $this->idemp_presu; }
    public function setIdEmpPresu($idemp_presu) { $this->idemp_presu = $idemp_presu; }

    public function getNomEmpPresu() { return $this->nom_emp_presu; }
    public function setNomEmpPresu($nom_emp_presu) { $this->nom_emp_presu = $nom_emp_presu; }

    public function getApeEmpPresu() { return $this->ape_emp_presu; }
    public function setApeEmpPresu($ape_emp_presu) { $this->ape_emp_presu = $ape_emp_presu; }

    public function getFechaIngElectro() { return $this->fecha_ing_electro; }
    public function setFechaIngElectro($fecha_ing_electro) { $this->fecha_ing_electro = $fecha_ing_electro; }

    public function getFechaEnvioPresup() { return $this->fecha_envio_presup; }
    public function setFechaEnvioPresup($fecha_envio_presup) { $this->fecha_envio_presup = $fecha_envio_presup; }

    public function getPresupuesto() { return $this->presupuesto; }
    public function setPresupuesto($presupuesto) { $this->presupuesto = $presupuesto; }

    public function getFechaConfirmReparacion() { return $this->fecha_confirm_reparacion; }
    public function setFechaConfirmReparacion($fecha_confirm_reparacion) { $this->fecha_confirm_reparacion = $fecha_confirm_reparacion; }

    public function getConfirmaPresupuesto() { return $this->confirma_presupuesto; }
    public function setConfirmaPresupuesto($confirma_presupuesto) { $this->confirma_presupuesto = $confirma_presupuesto; }

    public function getEstadoPresu() { return $this->estado_presup; }
    public function setEstadoPresu($estado_presup) { $this->estado_presup = $estado_presup; }

    public function getObservaciones() { return $this->observaciones; }
    public function setObservaciones($observaciones) { $this->observaciones = $observaciones; }

    //cobros
    public function getIdCobro() { return $this->id_cobro; }
    public function setIdCobro($id_cobro) { $this->id_cobro = $id_cobro; }
   
    public function getFechaCobroIni() { return $this->fecha_cobro_ini; }
    public function setFechaCobroIni($fecha_cobro_ini) { $this->fecha_cobro_ini = $fecha_cobro_ini; }

    public function getMontoFijoIni() { return $this->monto_fijo_cobro_ini; }
    public function setMontoFijoIni($monto_fijo_cobro_ini) { $this->monto_fijo_cobro_ini = $monto_fijo_cobro_ini; }

    public function getNroComproIni() { return $this->nro_compro_inicial; }
    public function setNroComproIni($nro_compro_inicial) { $this->nro_compro_inicial = $nro_compro_inicial; }

    public function getMedioPagoIni() { return $this->medio_pago_inicial; }
    public function setMedioPagoIni($medio_pago_inicial) { $this->medio_pago_inicial = $medio_pago_inicial; }

    public function getFechaCobroFin() { return $this->fecha_cobro_final; }
    public function setFechaCobroFin($fecha_cobro_final) { $this->fecha_cobro_final = $fecha_cobro_final; }

    public function getMontoFinRepa() { return $this->monto_final_repa; }
    public function setMontoFinRepa($monto_final_repa) { $this->monto_final_repa = $monto_final_repa; }

    public function getMedioPagoFin() { return $this->medio_pago_final; }
    public function setMedioPagoFin($medio_pago_final) { $this->medio_pago_final = $medio_pago_final; }

    public function getNroComproFin() { return $this->nro_compro_final; }
    public function setNroComproFin($nro_compro_final) { $this->nro_compro_final = $nro_compro_final; }

    public function getComentariosCobro() { return $this->comentarios; }
    public function setComentariosCobro($comentarios) { $this->comentarios = $comentarios; }

    public function getDb() { return $this->db; }
    public function setDb($db) { $this->db = $db; }

}

?>