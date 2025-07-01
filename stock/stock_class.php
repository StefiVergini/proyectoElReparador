<?php
class Stock {

    //atributos
    private $idstock;
    private $desc_art;
    private $cantidad;
    private $tipo_stock;
    private $idprov;
    private $nomProv;
    private $estado;
    private $db;

    //constructor
    public function __construct($db) {
        $this->db = $db; // conexión PDO
    }

    //metodos
    public function buscarStock($buscar, $cantidad, $proveedor) {
        $query = "SELECT s.idstock, s.descripcion_art, s.cantidad, s.idproveedores, s.tipo_stock,s.estado_stock, p.nombre_prov 
                  FROM stock s 
                  LEFT JOIN proveedores p ON s.idproveedores = p.idproveedores 
                  WHERE 1";
        
        $params = [];
    
        // Filtro por descripción o ID de stock
        if (!empty($buscar)) {
            $query .= " AND (s.descripcion_art LIKE :buscar OR s.idstock = :buscarExacto)";
            $params['buscar'] = "%$buscar%";
            $params['buscarExacto'] = $buscar;
        }
    
        // Filtro por cantidad
        if ($cantidad === "sinArt") {
            $query .= " AND s.cantidad = 0";
        } elseif ($cantidad === "mayor10") {
            $query .= " AND s.cantidad > 9";
        } elseif ($cantidad === "menor10") {
            $query .= " AND s.cantidad BETWEEN 1 AND 9";
        }
    
        // Filtro por proveedor
        if (!empty($proveedor)) {
            $query .= " AND s.idproveedores = :proveedor";
            $params['proveedor'] = $proveedor;
        }
    
        $query .= " ORDER BY s.idstock";
    
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
    
        // Convertir los resultados en objetos Stock manualmente
        $articulos = [];

        // Recorre cada fila y crea un objeto Stock
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $articulo = new Stock($this->db);
            $articulo->setIdStock($fila['idstock']);
            $articulo->setDescArt($fila['descripcion_art']);
            $articulo->setCantidad($fila['cantidad']);
            $articulo->setTipoStock($fila['tipo_stock']);
            $articulo->setIdProv($fila['idproveedores']);
            $articulo->setNomProv($fila['nombre_prov']);
            $articulo->setEstadoStock($fila['estado_stock']);
            
            $articulos[] = $articulo;
        }

        return $articulos;

    }
    public function bindParamQuerys($query, $idstock=null, $descripcion = null, $cantidad = null, $tipo_stock = null, $idprov = null, $estado=null){

        $ps = $this->db->prepare($query);

        if ($descripcion !== null) {
            $ps->bindParam(':descripcion_art', $descripcion);
        }
        if ($cantidad !== null) {
            $ps->bindParam(':cantidad', $cantidad);
        }
        if ($tipo_stock !== null) {
            $ps->bindParam(':tipo_stock', $tipo_stock);
        }
        if ($idprov !== null) {
            $ps->bindParam(':idproveedores', $idprov);
        }
        if ($estado !== null) {
            $ps->bindParam(':estado_stock', $estado);
        }
        if ($idstock !== null) {
            $ps->bindParam(':idstock', $idstock);
        }
        return $ps->execute();

    }

    public function altaArticulos($articulos, $idproveedor) {
        try {
            $this->db->beginTransaction();
    
            $query = "INSERT INTO stock (descripcion_art, cantidad, tipo_stock, idproveedores, estado_stock) 
                      VALUES (:descripcion, 0, :tipo, :idproveedores, 0)";
            $stmt = $this->db->prepare($query);
    
            foreach ($articulos as $articulo) {
                $stmt->bindParam(':descripcion', $articulo->getDescArt());
                $stmt->bindParam(':tipo', $articulo->getTipoStock());
                $stmt->bindParam(':idproveedores', $idproveedor, PDO::PARAM_INT);
                $stmt->execute();
            }
    
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return "Error al agregar los artículos: " . $e->getMessage();
        }
    }
    public function leerArticulos() {
        try {
            $query = "SELECT s.idstock, s.descripcion_art, s.cantidad, s.tipo_stock, s.idproveedores, s.estado_stock, p.nombre_prov
             FROM stock as s
            INNER JOIN proveedores as p ON p.idproveedores = s.idproveedores";
            $ps = $this->db->prepare($query);
            $ps->execute();
            $articulos = [];

            // Recorre cada fila y crea un objeto Stock
            while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $articulo = new Stock($this->db);
                $articulo->setIdStock($fila['idstock']);
                $articulo->setDescArt($fila['descripcion_art']);
                $articulo->setCantidad($fila['cantidad']);
                $articulo->setTipoStock($fila['tipo_stock']);
                $articulo->setIdProv($fila['idproveedores']);
                $articulo->setNomProv($fila['nombre_prov']);
                $articulo->setEstadoStock($fila['estado_stock']);
                
                $articulos[] = $articulo;
            }

            return $articulos;

        } catch (Exception $e) {
            echo "Error al obtener los Artículos: " . $e->getMessage();
            return [];
        }
    }

    public function obtenerUnArt($id) {
        try {
            $query = "SELECT  s.idstock, s.descripcion_art, s.cantidad, s.tipo_stock, s.idproveedores, s.estado_stock, p.nombre_prov FROM stock as s
            INNER JOIN proveedores as p ON p.idproveedores = s.idproveedores
            WHERE idstock = :id";
            $ps = $this->db->prepare($query);
            $ps->bindParam(':id', $id, PDO::PARAM_INT);
            $ps->execute();
    
            if ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $articulo = new Stock($this->db);
                $articulo->setIdStock($fila['idstock']);
                $articulo->setDescArt($fila['descripcion_art']);
                $articulo->setCantidad($fila['cantidad']);
                $articulo->setTipoStock($fila['tipo_stock']);
                $articulo->setIdProv($fila['idproveedores']);
                $articulo->setNomProv($fila['nombre_prov']);
                $articulo->setEstadoStock($fila['estado_stock']);
                
                return $articulo;
            } else {
                return "<h2>Ups! no se han encontrado datos</h2>"; // Si no se encuentra el art
            }
    
        } catch (Exception $e) {
            echo "Error al obtener el artículo: " . $e->getMessage();
            return null; // Devuelve null en caso de error
        }
    }
    

    public function leerArtxProv($id){
        try {
            $query = "SELECT * FROM stock WHERE idproveedores = :id";
            $ps = $this->db->prepare($query);
            $ps->bindParam(':id', $id, PDO::PARAM_INT);
            $ps->execute();
    
            $articulos = [];

            // Recorre cada fila y crea un objeto Stock
            while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $articulo = new Stock($this->db);
                $articulo->setIdStock($fila['idstock']);
                $articulo->setDescArt($fila['descripcion_art']);
                $articulo->setCantidad($fila['cantidad']);
                $articulo->setTipoStock($fila['tipo_stock']);
                $articulo->setEstadoStock($fila['estado_stock']);
                
                $articulos[] = $articulo;
            }
    
            if (empty($articulos)) {
                return false; // Retorna false si no hay artículos
            } else {
                return $articulos; // Retorna todos los artículos
            }
        } catch (Exception $e) {
            echo "Error al obtener los Artículos: " . $e->getMessage();
            return null; // Devuelve null en caso de error
        }
    }
    
    public function modificarArt() {
        try {
            $query = "UPDATE stock SET descripcion_art = :descripcion_art, cantidad = :cantidad, tipo_stock = :tipo_stock, idproveedores = :idproveedores WHERE idstock = :idstock";
            
            $idstock = $this->getIdStock();
            $descripcion = $this->getDescArt();
            $cantidad=$this->getCantidad();
            $tipo_stock = $this->getTipoStock();
            $idprov = $this->getIdProv();

            $resultado = $this->bindParamQuerys($query, $descripcion, $cantidad, $tipo_stock, $idprov, $idstock);
            return $resultado;
            
        } catch (Exception $e) {
            echo "Error al modificar el artículo: " . $e->getMessage();
            echo "Error al modificar en la línea: " . $e->getLine();
            return false;
        }
    }
    
    public function bajaArticulo($idstock) {
        try {
            // Verificar si el artículo tiene stock disponible
            $sql = "SELECT cantidad FROM stock WHERE idstock = :idstock";
            $ps = $this->db->prepare($sql);
            $ps->bindParam(':idstock', $idstock, PDO::PARAM_INT);
            $ps->execute();
            $resultado = $ps->fetch(PDO::FETCH_ASSOC);
    
            if (!$resultado) {
                echo "Error: No se encontró el artículo con ID $idstock.";
                return false;
            }
    
            $cantidad = $resultado['cantidad'];
    
            if ($cantidad > 0) {
                // No se puede eliminar si aún hay stock disponible
                return false;
            }
    
            // Verificar si el artículo está en un pedido activo
            $sql = "SELECT COUNT(*) as total 
                    FROM pedidos p
                    JOIN pedidos_desc pd ON p.id_ped = pd.id_pedido
                    WHERE pd.idstock = :idstock AND p.estado_pedido = '1'";
    
            $ps = $this->db->prepare($sql);
            $ps->bindParam(':idstock', $idstock, PDO::PARAM_INT);
            $ps->execute();
            $pedidoActivo = $ps->fetch(PDO::FETCH_ASSOC);
    
            if ($pedidoActivo['total'] > 0) {
                echo "Error: No se puede eliminar el artículo porque está en un pedido activo.";
                return false;
            }
    
            // Si no tiene stock y no está en un pedido activo, eliminar el artículo
            $query = "DELETE FROM stock WHERE idstock = :idstock";
            $ps = $this->db->prepare($query);
            $ps->bindParam(':idstock', $idstock, PDO::PARAM_INT);
    
            return $ps->execute();
        } catch (Exception $e) {
            echo "Error al dar de baja el artículo: " . $e->getMessage();
            return false;
        }
    }
    

    public function leerProv(){
        try{
            $query= "SELECT idproveedores, nombre_prov FROM proveedores ORDER BY nombre_prov";
            $ps = $this->db->prepare($query);
            $ps->execute();
            $proveedores = [];
            

            while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $proveedor = new Stock($this->db);
                $proveedor->setIdProv($fila['idproveedores']);
                $proveedor->setNomProv($fila['nombre_prov']);
                $proveedores[] = $proveedor;
            }
    
            return $proveedores;
    
        } catch (Exception $e) {
            echo "Error al obtener los Proveedores: " . $e->getMessage();
            return [];
        }
    }

    //getters 
    public function getIdStock(){
        return $this->idstock;
    }
    public function getDescArt(){
        return ucwords($this->desc_art);
    }
    public function getCantidad(){
        return $this->cantidad;
    }
    public function getTipoStock(){
        return ucwords($this->tipo_stock);
    }
    public function getIdProv(){
        return $this->idprov;
    }
    public function getNomProv(){
        return ucwords($this->nomProv);
    }
    public function getEstadoStock(){
        return $this->estado;
    }

    //setters 
    public function setIdStock($idstock){
        $this->idstock = $idstock;
    }
    public function setDescArt($desc_art){
        $this->desc_art = $desc_art;
    }
    public function setCantidad($cantidad){
        $this->cantidad = $cantidad;
    }
    public function setTipoStock($tipo_stock){
        $this->tipo_stock = $tipo_stock;
    }
    public function setIdProv($idprov){
        $this->idprov = $idprov;
    }
    public function setNomProv($nomProv){
        $this->nomProv = $nomProv;
    }
    public function setEstadoStock($estado){
        $this->estado = $estado;
    }

}

?>