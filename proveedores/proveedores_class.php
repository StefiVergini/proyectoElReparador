<?php
class Proveedores {

    //atributos
    private $id_prov;
    private $cuit;
    private $nom_prov;
    private $tel_prov;
    private $dir_prov;
    private $email_prov;
    private $saldo;
    private $estado_prov;
    private $db;

    //constructor
    public function __construct($db) {
        $this->db = $db; // conexión PDO
    }

    //metodos
    public function buscarProveedores($buscar) {
        $query = "SELECT * FROM proveedores WHERE idproveedores = :buscar OR cuit LIKE :buscarCuit OR nombre_prov LIKE :buscarNom";
        $ps = $this->db->prepare($query);
        
        $buscarComo = "%" . $buscar . "%";
        $buscarCuit = "%" . $buscar . "%";
        $ps->bindParam(':buscarCuit', $buscarCuit);
        $ps->bindParam(':buscar', $buscar);
        $ps->bindParam(':buscarNom', $buscarComo);
        
        $ps->execute();

        $resultados = $ps->fetchAll(PDO::FETCH_ASSOC);
        $proveedores = [];
        
        foreach ($resultados as $resultado) {
            $proveedor = new Proveedores($this->db);
            $proveedor->setIdProv($resultado['idproveedores']);
            $proveedor->setCuit($resultado['cuit']);
            $proveedor->setNomProv($resultado['nombre_prov']);
            $proveedor->setTelProv($resultado['tel_prov']);
            $proveedor->setDirProv($resultado['dir_prov']);
            $proveedor->setEmailProv($resultado['email_prov']);
            $proveedor->setSaldo($resultado['saldo']);
            $proveedor->setEstadoProv($resultado['estado_prov']);
            
            $proveedores[] = $proveedor;
        }
        
        return $proveedores;
    }

    public function filtrarPedidos($desde, $hasta, $proveedor) {
        $query = "SELECT p.id_ped, p.fecha_pedido, p.fecha_ingreso, p.idproveedores, p.idempleados, p.estado_pedido, 
                         prov.nombre_prov, prov.cuit, e.nom_empleado, e.ape_empleado,
                         pd.idstock, s.descripcion_art, pd.cant_pedida, pd.cant_ingresa
                  FROM pedidos p
                  INNER JOIN proveedores prov ON p.idproveedores = prov.idproveedores
                  INNER JOIN empleados e ON p.idempleados = e.idempleados
                  INNER JOIN pedidos_desc pd ON pd.id_pedido = p.id_ped
                  INNER JOIN stock s ON s.idstock = pd.idstock
                  WHERE 1";
        
        $params = [];
        
        // Filtro por rango de fechas
        if (!empty($desde) && !empty($hasta)) {
            $desde = date('Y-m-d 00:00:00', strtotime($desde));
            $hasta = date('Y-m-d 23:59:59', strtotime($hasta));
        
            $query .= " AND p.fecha_pedido BETWEEN :desde AND :hasta";
            $params['desde'] = $desde;
            $params['hasta'] = $hasta;
        }
        /*if (!empty($hasta)) {
            $query .= " AND p.fecha_pedido <= :hasta";
            $params['hasta'] = $hasta;
        }*/
        
        // Filtro por proveedor
        if (!empty($proveedor)) {
            $query .= " AND p.idproveedores = :proveedor";
            $params['proveedor'] = $proveedor;
        }
        
        $query .= " ORDER BY p.fecha_pedido DESC";
        
        // Preparar y ejecutar la consulta
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        // Obtener los resultados
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Procesar los resultados
        $pedidos = [];
        foreach ($resultados as $fila) {
            $id_pedido = $fila['id_ped'];
    
            // Si el pedido no está en el array, inicialízalo
            if (!isset($pedidos[$id_pedido])) {
                $pedidos[$id_pedido] = [
                    'id_ped' => $id_pedido,
                    'fecha_pedido' => $fila['fecha_pedido'],
                    'fecha_ingreso' => $fila['fecha_ingreso'],
                    'estado_pedido' => $fila['estado_pedido'],
                    'nom_empleado' => $fila['nom_empleado'],
                    'ape_empleado' => $fila['ape_empleado'],
                    'idempleados' => $fila['idempleados'],
                    'nombre_prov' => $fila['nombre_prov'],
                    'cuit' => $fila['cuit'],
                    'articulos' => [] // Lista de artículos
                ];
            }
    
            // Agregar el artículo a la lista del pedido
            $pedidos[$id_pedido]['articulos'][] = [
                'idstock' => $fila['idstock'],
                'descripcion_art' => $fila['descripcion_art'],
                'cant_pedida' => $fila['cant_pedida'],
                'cant_ingresa' => $fila['cant_ingresa']
            ];
        }
    
        // Devolver los pedidos formateados
        return array_values($pedidos);
    }
    
    public function esCuitUnico($cuit, $id = null) {
        try {
            // Consulta para verificar si el CUIT ya existe
            $queryCuit = "SELECT idproveedores FROM proveedores WHERE cuit = :cuit";
    
            // Si es modificación no contar el mismo proveedor
            if ($id !== null) {
                $queryCuit .= " AND idproveedores != :id";
            }
    
            $psCuit = $this->db->prepare($queryCuit);
            $psCuit->bindParam(':cuit', $cuit);
    
            if ($id !== null) {
                $psCuit->bindParam(':id', $id);
            }
    
            $psCuit->execute();
            $proveedorExistente = $psCuit->fetch(PDO::FETCH_ASSOC);
    
            // Si hay un resultado, significa que el CUIT ya está en uso
            return !$proveedorExistente;
    
        } catch (Exception $e) {
            echo "Error al verificar el CUIT: " . $e->getMessage();
            return false;
        }
    }
    public function bindParamQuerys($query, $id=null,$cuit = null, $nombre = null, $telefono = null, $direccion = null, $email=null, $saldo=null, $estado=null){

        $ps = $this->db->prepare($query);

        if ($id !== null) {
            $ps->bindParam(':id', $id);
        }
        if ($cuit !== null) {
            $ps->bindParam(':cuit_prov', $cuit);
        }
        if ($nombre !== null) {
            $ps->bindParam(':nom_prov', $nombre);
        }
        if ($telefono !== null) {
            $ps->bindParam(':tel_prov', $telefono);
        }
        if ($direccion !== null) {
            $ps->bindParam(':dir_prov', $direccion);
        }
        if ($email !== null) {
            $ps->bindParam(':email_prov', $email);
        }
        if ($saldo !== null) {
            $ps->bindParam(':saldo', $saldo);
        }
        if ($estado !== null) {
            $ps->bindParam(':estado', $estado);
        }
        return $ps->execute();

    }

    public function altaProv(){
        try {
            $cuit = $this->getCuit();
    
            // Verificar si el CUIT es único
            if (!$this->esCuitUnico($cuit)) {
                echo "<h2>Error: El CUIT ya está en uso por otro proveedor. </h2>";
                echo "<h3>No puede haber dos CUIT iguales.</h3>";
                return false;
            }
    
            $query = "INSERT INTO proveedores (cuit, nombre_prov, tel_prov, dir_prov, email_prov, saldo, estado_prov) 
                    VALUES (:cuit_prov, :nom_prov, :tel_prov, :dir_prov, :email_prov, :saldo, :estado)";
           
    
            $nombre = $this->getNomProv();
            $telefono = $this->getTelProv();
            $direccion = $this->getDirProv();
            $email = $this->getEmailProv();
            $saldo = $this->getSaldo();
            $estado = $this->getEstadoProv();
            
            $resultado = $this->bindParamQuerys($query, $id=null, $cuit, $nombre, $telefono, $direccion, $email, $saldo, $estado);
            return $resultado;

        } catch (Exception $e) {
            echo "Error al crear el proveedor: " . $e->getMessage();
            return false;
        }
    }
    public function leerProveedores() {
        try {
            $query = "SELECT * FROM proveedores";
            $ps = $this->db->prepare($query);
            $ps->execute();
            $proveedores = [];

            // Recorre cada fila y crea un objeto Proveedores
            while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $proveedor = new Proveedores($this->db);
                $proveedor->setIdProv($fila['idproveedores']);
                $proveedor->setCuit($fila['cuit']);
                $proveedor->setNomProv($fila['nombre_prov']);
                $proveedor->setTelProv($fila['tel_prov']);
                $proveedor->setDirProv($fila['dir_prov']);
                $proveedor->setEmailProv($fila['email_prov']);
                $proveedor->setSaldo($fila['saldo']);
                $proveedor->setEstadoProv($fila['estado_prov']);
                
                $proveedores[] = $proveedor;
            }

            return $proveedores;

        } catch (Exception $e) {
            echo "Error al obtener los proveedores: " . $e->getMessage();
            return [];
        }
    }

    public function obtenerUnProv($id) {
        try {
            $query = "SELECT * FROM proveedores WHERE idproveedores = :id";
            $ps = $this->db->prepare($query);
            $ps->bindParam(':id', $id, PDO::PARAM_INT);
            $ps->execute();
    
            if ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $proveedor = new Proveedores($this->db);
                $proveedor->setIdProv($fila['idproveedores']);
                $proveedor->setCuit($fila['cuit']);
                $proveedor->setNomProv($fila['nombre_prov']);
                $proveedor->setTelProv($fila['tel_prov']);
                $proveedor->setDirProv($fila['dir_prov']);
                $proveedor->setEmailProv($fila['email_prov']);
                $proveedor->setSaldo($fila['saldo']);
                $proveedor->setEstadoProv($fila['estado_prov']);
                
                return $proveedor;
            } else {
                return "<h2>Ups! no se han encontrado datos</h2>"; // Si no se encuentra el proveedor
            }
    
        } catch (Exception $e) {
            echo "Error al obtener el proveedor: " . $e->getMessage();
            return null; // Devuelve null en caso de error
        }
    }
    public function obtenerUnCuit($cuit) {
        try {
            $query = "SELECT * FROM proveedores WHERE cuit = :cuit";
            $ps = $this->db->prepare($query);
            $ps->bindParam(':cuit', $cuit);
            $ps->execute();
    
            if ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $proveedor = new Proveedores($this->db);
                $proveedor->setIdProv($fila['idproveedores']);
                $proveedor->setCuit($fila['cuit']);
                $proveedor->setNomProv($fila['nombre_prov']);
                $proveedor->setTelProv($fila['tel_prov']);
                $proveedor->setDirProv($fila['dir_prov']);
                $proveedor->setEmailProv($fila['email_prov']);
                $proveedor->setSaldo($fila['saldo']);
                $proveedor->setEstadoProv($fila['estado_prov']);
                
                return $proveedor;
            } else {
                return "<h2>Ups! no se han encontrado datos</h2>"; // Si no se encuentra el proveedor
            }
    
        } catch (Exception $e) {
            echo "Error al obtener el proveedor: " . $e->getMessage();
            return null; // Devuelve null en caso de error
        }
    }
    public function obtenerTodosLosPedidos() {
        try{
            $sql = "SELECT p.id_ped, p.fecha_pedido, p.fecha_ingreso, p.estado_pedido, 
                       e.nom_empleado, e.ape_empleado, pr.cuit,
                       pr.nombre_prov, e.idempleados, pd.idstock,
                       s.descripcion_art, pd.cant_pedida, pd.cant_ingresa 
                FROM pedidos p
                INNER JOIN empleados e ON p.idempleados = e.idempleados
                INNER JOIN proveedores pr ON p.idproveedores = pr.idproveedores
                INNER JOIN pedidos_desc pd ON p.id_ped = pd.id_pedido
                INNER JOIN stock s ON pd.idstock = s.idstock
                ORDER BY p.fecha_pedido DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $pedidos = [];
            foreach ($resultados as $fila) {
                $id_pedido = $fila['id_ped'];

                // Si el pedido no está en el array, se inicializa
                if (!isset($pedidos[$id_pedido])) {
                    $pedidos[$id_pedido] = [
                        'id_ped' => $id_pedido,
                        'fecha_pedido' => $fila['fecha_pedido'],
                        'fecha_ingreso' => $fila['fecha_ingreso'],
                        'estado_pedido' => $fila['estado_pedido'],
                        'nom_empleado' => $fila['nom_empleado'],
                        'ape_empleado' => $fila['ape_empleado'],
                        'idempleados' => $fila['idempleados'],
                        'nombre_prov' => $fila['nombre_prov'],
                        'cuit' => $fila['cuit'],
                        'articulos' => [] // Lista de artículos
                    ];
                }

                // Agregar el artículo a la lista del pedido
                $pedidos[$id_pedido]['articulos'][] = [
                    'idstock' => $fila['idstock'],
                    'descripcion_art' => $fila['descripcion_art'],
                    'cant_pedida' => $fila['cant_pedida'],
                    'cant_ingresa' => $fila['cant_ingresa']
                ];
            }

            return array_values($pedidos); // Convertimos el array asociativo en un array indexado
        } catch (PDOException $e) {
            error_log("Error en obtenerPedidosActivos: " . $e->getMessage());
            return [];
        }
    }
    public function finalizarPedido($id_ped,$fecha_fin, $cant_ingresa){
        try {
            $this->db->beginTransaction(); // Inicia una transacción
    
            // **1. Actualizar la fecha de finalización en la tabla de pedidos**
            $sql = "UPDATE pedidos SET fecha_ingreso = :fecha_fin, estado_pedido = 0 WHERE id_ped = :id_ped";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['fecha_fin' => $fecha_fin, 'id_ped' => $id_ped]);
    
            // **2. Insertar las cantidades ingresadas y actualizar stock**
            foreach ($cant_ingresa as $idstock => $cantidad) {
                $cantidad = intval($cantidad); // Convertir a número entero para seguridad
                if ($cantidad >= 0) {
                    // **Registrar la entrada en la tabla de stock**
                    $sqlStock = "UPDATE stock SET cantidad = cantidad + :cantidad WHERE idstock = :idstock";
                    $stmtStock = $this->db->prepare($sqlStock);
                    $stmtStock->execute(['cantidad' => $cantidad, 'idstock' => $idstock]);
    
                    // **Registrar en historial de movimientos (si aplica)**
                    $sqlHistorial = "UPDATE pedidos_desc SET cant_ingresa = :cantidad WHERE idstock = :idstock AND id_pedido = :id_ped AND cant_ingresa IS NULL";
                    $stmtHistorial = $this->db->prepare($sqlHistorial);
                    $stmtHistorial->execute(['idstock' => $idstock, 'id_ped' => $id_ped,'cantidad' => $cantidad]);
                }
            }
    
            $this->db->commit(); // Confirma transacción
    
            return true;
    
        } catch (Exception $e) {
            $this->db->rollBack(); // Revertir cambios si ocurre un error
            die("Error al finalizar el pedido: " . $e->getMessage());
        }
    
    }

    public function leerArtProv($id){
        try {
            $query = "SELECT * FROM stock WHERE idproveedores = :id";
            $ps = $this->db->prepare($query);
            $ps->bindParam(':id', $id, PDO::PARAM_INT);
            $ps->execute();
    
            $artProveedor = [];
            while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $artProveedor[] = [
                    'idStock' => $fila['idstock'],
                    'art' => $fila['descripcion_art'],
                    'cantDispo' => $fila['cantidad']
                ];
            }
    
            if (empty($artProveedor)) {
                return false; // Retorna false si no hay artículos
            } else {
                return $artProveedor; // Retorna todos los artículos
            }
        } catch (Exception $e) {
            echo "Error al obtener los Artículos: " . $e->getMessage();
            return null; // Devuelve null en caso de error
        }
    }


    public function guardarPedido($idEmp,$idProveedor, $detallesPedido) {
        try {
            $this->db->beginTransaction();

            // Insertar el pedido en la tabla "pedidos"
            $sqlPedido = "INSERT INTO pedidos (idempleados,idproveedores, fecha_pedido) VALUES (:id_emp,:id_proveedor, NOW())";
            $stmt = $this->db->prepare($sqlPedido);
            $stmt->execute([
                'id_emp' => $idEmp,
                'id_proveedor' => $idProveedor
            ]);
            $idPedido = $this->db->lastInsertId();

            // Insertar los artículos en la tabla "detalle_pedidos"
            $sqlDetalle = "INSERT INTO pedidos_desc (id_pedido, idstock, cant_pedida) VALUES (:id_pedido, :id_stock, :cantidad_pedida)";
            $stmtDetalle = $this->db->prepare($sqlDetalle);

            foreach ($detallesPedido as $detalle) {
                $stmtDetalle->execute([
                    'id_pedido' => $idPedido,
                    'id_stock' => $detalle['id_stock'],
                    'cantidad_pedida' => $detalle['cantidad']
                ]);
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return "Error al guardar el pedido: " . $e->getMessage();
        }
    }
    public function modificarProv() {
        try {
            $id = $this->getIdProv();
            $cuit = $this->getCuit();
    
            // Verificar si el CUIT es único
            if (!$this->esCuitUnico($cuit, $id)) {
                echo "<h2>Error: El CUIT ya está en uso por otro proveedor. </h2>";
                echo "<h3>No puede haber dos CUIT iguales.</h3>";
                return false;
            }
    
            // Si el CUIT no está en uso, se puede actualizar
            $query = "UPDATE proveedores SET cuit = :cuit_prov, nombre_prov = :nom_prov, tel_prov = :tel_prov, dir_prov = :dir_prov, email_prov = :email_prov, saldo = :saldo WHERE idproveedores = :id";
            
    
            $nombre = $this->getNomProv();
            $telefono = $this->getTelProv();
            $direccion = $this->getDirProv();
            $email = $this->getEmailProv();
            $saldo = $this->getSaldo();

            $resultado = $this->bindParamQuerys($query, $id, $cuit, $nombre, $telefono, $direccion, $email, $saldo, $estado=null);
            return $resultado;
            
        } catch (Exception $e) {
            echo "Error al modificar el proveedor: " . $e->getMessage();
            return false;
        }
    }
    
    public function bajaProveedor($id) {
        try {
            $sql = "SELECT COUNT(*) FROM pedidos WHERE idproveedores = :id AND estado_pedido = 1";
            $ps = $this->db->prepare($sql);
            $ps->bindParam(':id', $id, PDO::PARAM_INT);
            $ps->execute();
            $tienePedidosActivos = $ps->fetchColumn(); // Devuelve el número de pedidos activos
            
            if ($tienePedidosActivos > 0) {
                return false;
            } else {
                $query = "UPDATE proveedores SET estado_prov = 0 WHERE idproveedores = :id";
                $ps = $this->db->prepare($query);
                $ps->bindParam(':id', $id, PDO::PARAM_INT);
                return $ps->execute();
            }
            
        } catch (Exception $e) {
            echo "Error al dar de baja al proveedor: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarProveedor($id) {
        try {
            $query = "UPDATE proveedores SET estado_prov = 1 WHERE idproveedores = :id";
            $ps = $this->db->prepare($query);
            $ps->bindParam(':id', $id, PDO::PARAM_INT);
            return $ps->execute();
        } catch (Exception $e) {
            echo "Error al dar de alta al proveedor: " . $e->getMessage();
            return false;
        }
    }

    //getters 
    public function getIdProv(){
        return $this->id_prov;
    }
    public function getCuit(){
        return $this->cuit;
    }
    public function getNomProv(){
        return ucwords($this->nom_prov);
    }
    public function getTelProv(){
        return $this->tel_prov;
    }
    public function getDirProv(){
        return ucwords($this->dir_prov);
    }
    public function getEmailProv(){
        return $this->email_prov;
    }
    public function getSaldo(){
        return $this->saldo;
    }
    public function getEstadoProv(){
        return $this->estado_prov;
    }

    //setters 
    public function setIdProv($id_prov){
        $this->id_prov = $id_prov;
    }
    public function setCuit($cuit){
        $this->cuit = $cuit;
    }
    public function setNomProv($nom_prov){
        $this->nom_prov = $nom_prov;
    }
    public function setTelProv($tel_prov){
        $this->tel_prov = $tel_prov;
    }
    public function setDirProv($dir_prov){
        $this->dir_prov = $dir_prov;
    }
    public function setEmailProv($email_prov){
        $this->email_prov = $email_prov;
    }
    public function setSaldo($saldo){
        $this->saldo = $saldo;
    }
    public function setEstadoProv($estado_prov){
        $this->estado_prov = $estado_prov;
    }


}

?>