<?php

    class Clientes{

        //atributos
        private $id_cli;
        private $dni_cli;
        private $nom_cli;
        private $ape_cli;
        private $tel_cli;
        private $dir_cli;
        private $email_cli;
        private $estado_cli;
        private $db;

        //constructor
        public function __construct($db) {
            $this->db = $db; // conexión PDO
        }

        //metodos
        public function buscarClientes($buscar) {
            $query = "SELECT * FROM clientes WHERE idclientes = :buscar OR dni_cliente LIKE :buscarDni OR nom_cliente LIKE :buscarNom";
            $ps = $this->db->prepare($query);
            
            $buscarComo = "%" . $buscar . "%";
            $buscarDni = "%" . $buscar . "%";
            $ps->bindParam(':buscarDni', $buscarDni);
            $ps->bindParam(':buscar', $buscar);
            $ps->bindParam(':buscarNom', $buscarComo);
            
            $ps->execute();
    
            $resultados = $ps->fetchAll(PDO::FETCH_ASSOC);
            $clientes = [];
            
            foreach ($resultados as $resultado) {
                $cliente = new Clientes($this->db);
                $cliente->setIdCli($resultado['idclientes']);
                $cliente->setDniCli($resultado['dni_cliente']);
                $cliente->setNomCli($resultado['nom_cliente']);
                $cliente->setApeCli($resultado['ape_cliente']);
                $cliente->setTelCli($resultado['tel_cliente']);
                $cliente->setDirCli($resultado['dir_cliente']);
                $cliente->setEmailCli($resultado['email_cliente']);
                $cliente->setEstadoCli($resultado['estado_cliente']);
                
                $clientes[] = $cliente;
            }
            
            return $clientes;
        }

        public function esDniUnico($dni, $id = null) {
            try {
                // Consulta para verificar si el CUIT ya existe
                $queryDni = "SELECT idclientes FROM clientes WHERE dni_cliente = :dni";
        
                // Si es modificación no contar el mismo cliente
                if ($id !== null) {
                    $queryDni .= " AND idclientes != :id";
                }
        
                $psDni = $this->db->prepare($queryDni);
                $psDni->bindParam(':dni', $dni);
        
                if ($id !== null) {
                    $psDni->bindParam(':id', $id);
                }
        
                $psDni->execute();
                $clienteExistente = $psDni->fetch(PDO::FETCH_ASSOC);
        
                // Si hay un resultado, significa que el DNI ya está en uso
                return !$clienteExistente;
        
            } catch (Exception $e) {
                echo "Error al verificar el DNI: " . $e->getMessage();
                return false;
            }
        }

        public function altaCli(){
            try {
                $dni = $this->getDniCli();
        
                // Verificar si el DNI es único
                if (!$this->esDniUnico($dni)) {
                    echo "<h2>Error: El DNI ya está en uso por otro cliente.<br>No puede haber dos DNI iguales. </h2>";
                    return false;
                }
        
                $query = "INSERT INTO clientes (dni_cliente, nom_cliente,ape_cliente, tel_cliente, dir_cliente, email_cliente, estado_cliente) 
                        VALUES (:dni_cli, :nom_cli, :ape_cli, :tel_cli, :dir_cli, :email_cli, :estado)";
               
        
                $nombre = $this->getNomCli();
                $apellido = $this->getApeCli();
                $telefono = $this->getTelCli();
                $direccion = $this->getDirCli();
                $email = $this->getEmailCli();
                $estado = $this->getEstadoCli();
                
                $resultado = $this->bindParamQuerys($query,null, $dni, $nombre, $apellido, $telefono, $direccion, $email, $estado);
                return $resultado;
    
            } catch (Exception $e) {
                echo "Error al crear el cliente: " . $e->getMessage();
                return false;
            }
        }

        public function bindParamQuerys($query, $id=null,$dni = null, $nombre = null,$apellido = null, $telefono = null, $direccion = null, $email=null,$estado=null){

            $ps = $this->db->prepare($query);
    
            if ($id !== null) {
                $ps->bindParam(':id', $id);
            }
            if ($dni !== null) {
                $ps->bindParam(':dni_cli', $dni);
            }
            if ($nombre !== null) {
                $ps->bindParam(':nom_cli', $nombre);
            }
            if ($apellido !== null) {
                $ps->bindParam(':ape_cli', $apellido);
            }
            if ($telefono !== null) {
                $ps->bindParam(':tel_cli', $telefono);
            }
            if ($direccion !== null) {
                $ps->bindParam(':dir_cli', $direccion);
            }
            if ($email !== null) {
                $ps->bindParam(':email_cli', $email);
            }
            if ($estado !== null) {
                $ps->bindParam(':estado', $estado);
            }
            return $ps->execute();
    
        }

        public function leerClientes() {
            try {
                $query = "SELECT * FROM clientes";
                $ps = $this->db->prepare($query);
                $ps->execute();
                $clientes = [];
    
                // Recorre cada fila y crea un objeto Proveedores
                while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                    $cliente = new Clientes($this->db);
                    $cliente->setIdCli($fila['idclientes']);
                    $cliente->setDniCli($fila['dni_cliente']);
                    $cliente->setNomCli($fila['nom_cliente']);
                    $cliente->setApeCli($fila['ape_cliente']);
                    $cliente->setTelCli($fila['tel_cliente']);
                    $cliente->setDirCli($fila['dir_cliente']);
                    $cliente->setEmailCli($fila['email_cliente']);
                    $cliente->setEstadoCli($fila['estado_cliente']);
                    
                    $clientes[] = $cliente;
                }
    
                return $clientes;
    
            } catch (Exception $e) {
                echo "Error al obtener los clientes: " . $e->getMessage();
                return [];
            }
        }

        public function obtenerUnCli($id) {
            try {
                $query = "SELECT * FROM clientes WHERE idclientes = :id";
                $ps = $this->db->prepare($query);
                $ps->bindParam(':id', $id, PDO::PARAM_INT);
                $ps->execute();
        
                if ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                    $cliente = new Clientes($this->db);
                    $cliente->setIdCli($fila['idclientes']);
                    $cliente->setDniCli($fila['dni_cliente']);
                    $cliente->setNomCli($fila['nom_cliente']);
                    $cliente->setApeCli($fila['ape_cliente']);
                    $cliente->setTelCli($fila['tel_cliente']);
                    $cliente->setDirCli($fila['dir_cliente']);
                    $cliente->setEmailCli($fila['email_cliente']);
                    $cliente->setEstadoCli($fila['estado_cliente']);
                    
                    return $cliente;
                } else {
                    return "<h2>Ups! no se ha encontrado al cliente</h2>"; // Si no se encuentra el cliente
                }
        
            } catch (Exception $e) {
                echo "Error al obtener el cliente: " . $e->getMessage();
                return null; // Devuelve null en caso de error
            }
        }
        public function obtenerUnDni($dni) {
            try {
                $query = "SELECT * FROM clientes WHERE dni_cliente = :dni";
                $ps = $this->db->prepare($query);
                $ps->bindParam(':dni', $dni);
                $ps->execute();
        
                if ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                    $cliente = new Clientes($this->db);
                    $cliente->setIdCli($fila['idclientes']);
                    $cliente->setDniCli($fila['dni_cliente']);
                    $cliente->setNomCli($fila['nom_cliente']);
                    $cliente->setApeCli($fila['ape_cliente']);
                    $cliente->setTelCli($fila['tel_cliente']);
                    $cliente->setDirCli($fila['dir_cliente']);
                    $cliente->setEmailCli($fila['email_cliente']);
                    $cliente->setEstadoCli($fila['estado_cliente']);
                    
                    return $cliente;
                } else {
                    return "<h2>Ups! no se ha encontrado al cliente</h2>"; // Si no se encuentra el cliente
                }
        
            } catch (Exception $e) {
                echo "Error al obtener el cliente: " . $e->getMessage();
                return null; // Devuelve null en caso de error
            }
        }
    
        public function modificarCli() {
            try {
                $id = $this->getIdCli();
                $dni = $this->getDniCli();
        
                // Verificar si el DNI es único
                if (!$this->esDniUnico($dni, $id)) {
                    echo "<h2>Error: El DNI ya está en uso por otro cliente.<br>No puede haber dos DNI iguales.</h2>";
                    return false;
                }
        
                // Si el DNI no está en uso, se puede actualizar
                $query = "UPDATE clientes SET dni_cliente = :dni_cli, nom_cliente = :nom_cli, ape_cliente = :ape_cli, tel_cliente = :tel_cli, dir_cliente = :dir_cli, email_cliente = :email_cli WHERE idclientes = :id";
                
        
                $nombre = $this->getNomCli();
                $apellido = $this->getApeCli();
                $telefono = $this->getTelCli();
                $direccion = $this->getDirCli();
                $email = $this->getEmailCli();

    
                $resultado = $this->bindParamQuerys($query, $id, $dni, $nombre, $apellido, $telefono, $direccion, $email, $estado=null);
                return $resultado;
                
            } catch (Exception $e) {
                echo "Error al modificar el cliente: " . $e->getMessage();
                return false;
            }
        }
        
        public function bajaCliente($id) {
            //Verificar electrodomesticos a reparar, si tiene en reparacion activa
            //no dar de baja
            //deberia agregar un campo comentarios para la baja
            //ejemplo debe $tanto dinero fecha tanto
            $queryCheck = "SELECT e.marca, e.modelo, t.nom_tipo, r.estado_reparacion 
                   FROM electrodomesticos AS e
                   INNER JOIN tipo_electro AS t ON e.tipo_electro = t.idtipo_electro
                   INNER JOIN reparaciones AS r ON e.idelectrodomesticos = r.idelectrodomesticos
                   WHERE e.idclientes = :id AND r.estado_reparacion = 1";
    
            $ps = $this->db->prepare($queryCheck);
            $ps->bindParam(':id', $id, PDO::PARAM_INT);
            $ps->execute();
    
            $electrodomesticosActivos = [];
    
            while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $electrodomesticosActivos[] = [
                    'marca' => $fila['marca'],
                    'modelo' => $fila['modelo'],
                    'tipo' => $fila['nom_tipo']
                ];
            }
            
            // Si hay reparaciones activas, notificar al usuario
            if (!empty($electrodomesticosActivos)) {
                echo "<h2>Error:</h2>";
                echo "<h2 style='text-align:center;'>No se puede dar de baja al cliente.</h2>";
                echo "<h2 style='text-align:center;'>Tiene reparaciones activas en los siguientes electrodomésticos:\n</h2>";
                foreach ($electrodomesticosActivos as $electrodomestico) {
                    echo "<h2 style='text-align:center;'>-  Tipo: {$electrodomestico['tipo']} - Marca: {$electrodomestico['marca']} - Modelo: {$electrodomestico['modelo']}\n</h2>";
                }
                return false;
            }
            
            // Si no hay reparaciones activas, dar de baja al cliente
            try {
                $query = "UPDATE clientes SET estado_cliente = 0 WHERE idclientes = :id";
                $ps = $this->db->prepare($query);
                $ps->bindParam(':id', $id, PDO::PARAM_INT);
                $ps->execute();
                echo "<h2 style='text-align:center;'>El cliente ha sido dado de baja correctamente.</h2>";
                return true;
            } catch (Exception $e) {
                echo "Error al dar de baja al cliente: " . $e->getMessage();
                return false;
            }
        }

        public function actualizarCliente($id) {
            try {
                $query = "UPDATE clientes SET estado_cliente = 1 WHERE idclientes = :id";
                $ps = $this->db->prepare($query);
                $ps->bindParam(':id', $id, PDO::PARAM_INT);
                return $ps->execute();
            } catch (Exception $e) {
                echo "Error al dar de alta al cliente: " . $e->getMessage();
                return false;
            }
        }
        //getters 
        public function getIdCli(){
            return $this->id_cli;
        }
        public function getDniCli(){
            return $this->dni_cli;
        }
        public function getNomCli(){
            return ucwords($this->nom_cli);
        }
        public function getApeCli(){
            return ucwords($this->ape_cli);
        }
        public function getTelCli(){
            return $this->tel_cli;
        }
        public function getDirCli(){
            return ucwords($this->dir_cli);
        }
        public function getEmailCli(){
            return $this->email_cli;
        }
        public function getEstadoCli(){
            return $this->estado_cli;
        }

        //setters 
        public function setIdCli($id_cli){
            $this->id_cli = $id_cli;
        }
        public function setDniCli($dni_cli){
            $this->dni_cli = $dni_cli;
        }
        public function setNomCli($nom_cli){
            $this->nom_cli = $nom_cli;
        }
        public function setApeCli($ape_cli){
            $this->ape_cli = $ape_cli;
        }
        public function setTelCli($tel_cli){
            $this->tel_cli = $tel_cli;
        }
        public function setDirCli($dir_cli){
            $this->dir_cli = $dir_cli;
        }
        public function setEmailCli($email_cli){
            $this->email_cli = $email_cli;
        }
        public function setEstadoCli($estado_cli){
            $this->estado_cli = $estado_cli;
        }

    }

?>