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
            $query = "UPDATE proveedores SET estado_prov = 0 WHERE idproveedores = :id";
            $ps = $this->db->prepare($query);
            $ps->bindParam(':id', $id, PDO::PARAM_INT);
            return $ps->execute();
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