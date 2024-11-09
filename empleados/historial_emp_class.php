<?php
include_once 'categorias_emp_class.php'; 

class HistorialEmp{
    private $idHistorial;
    private $fecha_inicio;
    private $fecha_fin;
    private $estado_emp;
    private $id_emp;
    private $id_categoria;
    private $descripcion;
    private $db;

    public function __construct($db) {
        $this->db = $db; // conexión PDO
    }

    public function altaHistorial() {
        try {
            $query = "INSERT INTO historial_empleados (idempleados, idcategorias_empleados, fecha_inicio_puesto, estado_empleado) 
                      VALUES (:id_emp, :id_categoria, :fecha_inicio, :estado_emp)";
            $ps = $this->db->prepare($query);
            $ps->bindParam(':id_emp', $this->id_emp);
            $ps->bindParam(':id_categoria', $this->id_categoria);
            $ps->bindParam(':fecha_inicio', $this->fecha_inicio);
            $ps->bindParam(':estado_emp', $this->estado_emp);
            
            return $ps->execute();
        } catch (Exception $e) {
            echo "Error al crear historial: " . $e->getMessage();
            return false;
        }
    }

    public function registroHistorico($idh){
        try {
            $query= "SELECT h.idcategorias_empleados, c.tipo_empleado, h.fecha_inicio_puesto, h.fecha_fin_puesto, h.descripcion_cambio 
                     FROM historial_empleados AS h 
                     INNER JOIN categorias_empleados AS c 
                     ON c.idcategorias_empleados = h.idcategorias_empleados
                     WHERE h.idempleados = :idh";
    
            $ps = $this->db->prepare($query);
            // Aquí agregas la vinculación del parámetro :id
            $ps->bindParam(':idh', $idh, PDO::PARAM_INT);
            $ps->execute();
    
            $cat_fecha = [];
            while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                // Instanciar las clases
                $historial = new HistorialEmp($this->db);
                $categoria = new CategoriasEmp($this->db);
    
                $historial->setIdCategoria($fila['idcategorias_empleados']);
                $historial->setFechaInicio($fila['fecha_inicio_puesto']);
                $historial->setFechaFin($fila['fecha_fin_puesto']);
                $historial->setDescripcion($fila['descripcion_cambio']);
    
                $categoria->setTipoEmp($fila['tipo_empleado']);
    
                $cat_fecha[] = [
                    'historial' => $historial,
                    'categoria' => $categoria
                ];
            }
    
            return $cat_fecha;
    
        } catch (Exception $e) {
            echo "Error al obtener el historial del Empleado: " . $e->getMessage();
            return [];
        }
    }

    public function leerCatFecha($id){
        try {
            $query= "SELECT h.idcategorias_empleados, c.tipo_empleado, h.fecha_inicio_puesto, h.descripcion_cambio 
                     FROM historial_empleados AS h 
                     INNER JOIN categorias_empleados AS c 
                     ON c.idcategorias_empleados = h.idcategorias_empleados 
                     WHERE h.fecha_inicio_puesto = (
                         SELECT MAX(h2.fecha_inicio_puesto) 
                         FROM historial_empleados AS h2 
                         WHERE h2.idempleados = :id)";
    
            $ps = $this->db->prepare($query);
            // Aquí agregas la vinculación del parámetro :id
            $ps->bindParam(':id', $id, PDO::PARAM_INT);
            $ps->execute();
    
            $cat_fecha = [];
            while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                // Instanciar las clases
                $historial = new HistorialEmp($this->db);
                $categoria = new CategoriasEmp($this->db);
    
                $historial->setIdCategoria($fila['idcategorias_empleados']);
                $historial->setFechaInicio($fila['fecha_inicio_puesto']);
                $historial->setDescripcion($fila['descripcion_cambio']);
    
                $categoria->setTipoEmp($fila['tipo_empleado']);
    
                $cat_fecha[] = [
                    'historial' => $historial,
                    'categoria' => $categoria
                ];
            }
    
            return $cat_fecha;
    
        } catch (Exception $e) {
            echo "Error al obtener el historial del Empleado: " . $e->getMessage();
            return [];
        }
    }
    

    public function modificarCatFecha($id){
        try {
            $query = "UPDATE historial_empleados 
                      SET fecha_inicio_puesto = :fecha_inicio, 
                          idcategorias_empleados = :id_categoria, 
                          descripcion_cambio = :descripcion
                      WHERE fecha_inicio_puesto = (
                          SELECT MAX(fecha_inicio_puesto) 
                          FROM historial_empleados 
                          WHERE idempleados = :id)";
            
            $ps = $this->db->prepare($query);
            $ps->bindParam(':id', $id, PDO::PARAM_INT);
            $ps->bindParam(':fecha_inicio', $this->fecha_inicio);
            $ps->bindParam(':id_categoria', $this->id_categoria);
            $ps->bindParam(':descripcion', $this->descripcion);
            
            return $ps->execute();
        } catch (Exception $e) {
            echo "Error al modificar historial del Empleado: " . $e->getMessage();
            return false;
        }
    }

    public function bajaEmpleado($id){
        try {
            $query = "UPDATE historial_empleados 
                      SET fecha_fin_puesto = :fecha_fin, 
                          estado_empleado = :estado_emp, 
                          descripcion_cambio = :descripcion
                      WHERE fecha_inicio_puesto = (
                          SELECT MAX(fecha_inicio_puesto) 
                          FROM historial_empleados 
                          WHERE idempleados = :id)";
            
            $ps = $this->db->prepare($query);
            $ps->bindParam(':id', $id, PDO::PARAM_INT);
            $ps->bindParam(':fecha_fin', $this->fecha_fin);
            $ps->bindParam(':estado_emp', $this->estado_emp);
            $ps->bindParam(':descripcion', $this->descripcion);
            
            return $ps->execute();
        } catch (Exception $e) {
            echo "Error al dar de baja al Empleado: " . $e->getMessage();
            return false;
        }
    }

    //Setters
    public function setFechaInicio($fecha_inicio) {
        $this->fecha_inicio = $fecha_inicio;
    }

    public function setFechaFin($fecha_fin) {
        $this->fecha_fin = $fecha_fin;
    }

    public function setEstadoEmp($estado_emp) {
        $this->estado_emp = $estado_emp;
    }

    public function setIdEmp($id_emp) {
        $this->id_emp = $id_emp;
    }

    public function setIdCategoria($id_categoria) {
        $this->id_categoria = $id_categoria;
    }
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    //Getters
    public function getFechaInicio() {
        return $this->fecha_inicio;
    }

    public function getFechaFin() {
        return $this->fecha_fin;
    }

    public function getEstadoEmp() {
        return $this->estado_emp;
    }

    public function getIdEmp() {
        return $this->id_emp;
    }

    public function getIdCategoria() {
        return $this->id_categoria;
    }
    public function getDescripcion() {
        return $this->descripcion;
    }

}



?>