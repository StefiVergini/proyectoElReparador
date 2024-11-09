<?php
class CategoriasEmp{

    private $id_categoria;
    private $tipo_empleado;
    private $sueldo_bruto;
    private $db;

    //constructor
    public function __construct($db) {
        $this->db = $db; // conexión PDO
    }

    public function altaCategoria(){
        try {  
            $query = "INSERT INTO categorias_empleados (tipo_empleado, sueldo_bruto) 
                    VALUES (:tipo_emp, :sueldo)";
           
            $tipo_emp = $this->getTipoEmp();
            $sueldo = $this->getSueldoBruto();
            
            $ps = $this->db->prepare($query);
            $ps->bindParam(':tipo_emp', $tipo_emp);
            $ps->bindParam(':sueldo', $sueldo);

            return $ps->execute();

        } catch (Exception $e) {
            echo "Error al crear Categoría: " . $e->getMessage();
            return false;
        }
    }
    public function leerCategorias(){
        try{
            $query= "SELECT idcategorias_empleados, tipo_empleado FROM categorias_empleados ORDER BY tipo_empleado";
            $ps = $this->db->prepare($query);
            $ps->execute();
            $categorias = [];
            
            // Recorrer resultados y crear objetos de la clase
            while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $categoria = new CategoriasEmp($this->db);
                $categoria->setIdCat($fila['idcategorias_empleados']);
                $categoria->setTipoEmp($fila['tipo_empleado']);
                $categorias[] = $categoria; // Agregar el objeto al array
            }
    
            return $categorias;
    
        } catch (Exception $e) {
            echo "Error al obtener las Categorías: " . $e->getMessage();
            return [];
        }
    }
    public function modificarCategoria() {
        try {
            $id_cat = $this->getIdCat();
    
            $query = "UPDATE categorias_empleados SET tipo_empleado = :tipo_emp, sueldo_bruto = :sueldo WHERE idcategorias_empleados = :id_cat";
            
    
            $ps = $this->db->prepare($query);
            $ps->bindParam(':tipo_emp', $tipo_emp);
            $ps->bindParam(':sueldo', $sueldo);

            return $ps->execute();
            
        } catch (Exception $e) {
            echo "Error al modificar el Categoría: " . $e->getMessage();
            return false;
        }
    }

    public function getIdCat(){
        return $this->id_categoria;
    }
    public function getTipoEmp(){
        return ucwords($this->tipo_empleado);
    }
    public function getSueldoBruto(){
        return $this->sueldo_bruto;
    }
    public function setIdCat($id_categoria){
        $this->id_categoria = $id_categoria;
    }
    public function setTipoEmp($tipo_empleado){
        $this->tipo_empleado = $tipo_empleado;
    }
    public function setSueldoBruto($sueldo_bruto){
        $this->sueldo_bruto = $sueldo_bruto;
    }
}


?>