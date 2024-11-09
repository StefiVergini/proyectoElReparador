<?php
require_once 'historial_emp_class.php';
require_once 'categorias_emp_class.php'; 

class Empleados {

    //atributos
    private $id_emp;
    private $dni_emp;
    private $nom_emp;
    private $ape_emp;
    private $tel_emp;
    private $email_emp;
    private $dir_emp;
    private $id_local;
    private $db;

    //constructor
    public function __construct($db) {
        $this->db = $db; // conexión PDO
    }

    //metodos
    public function buscarEmpleados($buscar) {
        $query = "SELECT e.idempleados, e.dni_empleado, e.nom_empleado, e.ape_empleado, e.tel_empleado, e.email_empleado, e.idlocal, 
                         h.fecha_inicio_puesto, h.idcategorias_empleados, c.tipo_empleado
                  FROM empleados AS e
                  INNER JOIN historial_empleados AS h 
                      ON e.idempleados = h.idempleados
                  INNER JOIN categorias_empleados AS c 
                      ON h.idcategorias_empleados = c.idcategorias_empleados
                  WHERE (e.idempleados = :buscar OR e.dni_empleado LIKE :buscarDni OR e.nom_empleado LIKE :buscarNom)
                  AND h.fecha_inicio_puesto = (
                      SELECT MAX(h2.fecha_inicio_puesto)
                      FROM historial_empleados AS h2
                      WHERE h2.idempleados = e.idempleados
                  )
                  AND h.estado_empleado = 1";
        
        $ps = $this->db->prepare($query);
        
        $buscarComo = "%" . $buscar . "%";
        $buscarDni = "%" . $buscar . "%";
        $ps->bindParam(':buscarDni', $buscarDni);
        $ps->bindParam(':buscar', $buscar);
        $ps->bindParam(':buscarNom', $buscarComo);
        
        $ps->execute();
    
        $resultados = $ps->fetchAll(PDO::FETCH_ASSOC);
        $empleados = [];
        
        foreach ($resultados as $resultado) {
            $empleado = new Empleados($this->db);
            $historial = new HistorialEmp($this->db);
            $categoria = new CategoriasEmp($this->db);
            
            // Asignar los datos del empleado
            $empleado->setIdEmp($resultado['idempleados']);
            $empleado->setDniEmp($resultado['dni_empleado']);
            $empleado->setNomEmp($resultado['nom_empleado']);
            $empleado->setApeEmp($resultado['ape_empleado']);
            $empleado->setTelEmp($resultado['tel_empleado']);
            $empleado->setEmailEmp($resultado['email_empleado']);
            $empleado->setIdLocal($resultado['idlocal']);
            
            $historial->setFechaInicio($resultado['fecha_inicio_puesto']);
            $historial->setIdCategoria($resultado['idcategorias_empleados']);
            
            $categoria->setTipoEmp($resultado['tipo_empleado']);
            
            $empleados[] = [
                'empleado' => $empleado,
                'historial' => $historial,
                'categoria' => $categoria
            ];
        }
        
        return $empleados;
    }
    public function esDniUnico($dni, $id = null) {
        try {
            // Consulta para verificar si el DNI ya existe
            $queryDni = "SELECT idempleados FROM empleados WHERE dni_empleado = :dni";
    
            // Si es modificación no contar el mismo empleado
            if ($id !== null) {
                $queryDni .= " AND idempleados != :id";
            }
    
            $ps = $this->db->prepare($queryDni);
            $ps->bindParam(':dni', $dni);
    
            if ($id !== null) {
                $ps->bindParam(':id', $id);
            }
    
            $ps->execute();
            $empleadoExistente = $ps->fetch(PDO::FETCH_ASSOC);
    
            // Si hay un resultado, significa que el DNI ya está en uso
            return !$empleadoExistente;
    
        } catch (Exception $e) {
            echo "Error al verificar el DNI: " . $e->getMessage();
            return false;
        }
    }
    public function bindParamQuerys($query, $id=null,$dni = null, $nombre = null, $apellido=null, $telefono = null, $email=null, $direccion = null,  $local=null){
        $ps = $this->db->prepare($query);

        if ($id !== null) {
            $ps->bindParam(':id', $id);
        }
        if ($dni !== null) {
            $ps->bindParam(':dni_emp', $dni);
        }
        if ($nombre !== null) {
            $ps->bindParam(':nom_emp', $nombre);
        }
        if ($apellido !== null) {
            $ps->bindParam(':ape_emp', $apellido);
        }
        if ($telefono !== null) {
            $ps->bindParam(':tel_emp', $telefono);
        }
        if ($email !== null) {
            $ps->bindParam(':email_emp', $email);
        }
        if ($direccion !== null) {
            $ps->bindParam(':dir_emp', $direccion);
        }
        if ($local !== null) {
            $ps->bindParam(':id_local', $local);
        }

        return $ps->execute();

    }

    public function leerLocales() {
        try {
            $query = "SELECT idlocal, dir_local FROM locales ORDER BY idlocal";
            $ps = $this->db->prepare($query);
            $ps->execute();
            $locales = [];
            
            // Guardar resultados en el array
            while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $locales[] = $fila; // Agregar cada fila al array
            }
            
            return $locales;
            
        } catch (Exception $e) {
            echo "Error al obtener los locales: " . $e->getMessage();
            return [];
        }
    }

    public function altaEmp($categoria_id, $fecha_inicio, $estado_emp) {
        try {
            // Iniciar la transacción si sale mal vuelve todo como estaba porque se modifican dos tablas
            $this->db->beginTransaction();
    
            // Consulta para insertar en la tabla empleados
            $queryEmpleado = "INSERT INTO empleados (dni_empleado, nom_empleado, ape_empleado, tel_empleado, email_empleado, dir_empleado, idlocal) 
                              VALUES (:dni_emp, :nom_emp, :ape_emp, :tel_emp, :email_emp, :dir_emp, :id_local)";
            
            // Llamar a bindParamQuerys para enlazar los valores de empleado
            // Aquí asumes que tienes métodos para obtener los valores correctos para los parámetros del empleado
            $dni = $this->getDniEmp(); 
            $nombre = $this->getNomEmp();
            $apellido = $this->getApeEmp();
            $telefono = $this->getTelEmp();
            $email = $this->getEmailEmp();
            $direccion = $this->getDirEmp();
            $local = $this->getIdLocal();

            $resultado = $this->bindParamQuerys($queryEmpleado, null, $dni, $nombre, $apellido, $telefono, $email, $direccion, $local);
            
            // Verificar si la inserción fue exitosa
            if (!$resultado) {
                throw new Exception("Error al insertar el empleado.");
            }
    
            // Obtener el ID del empleado recién insertado
            $idEmpleado = $this->db->lastInsertId(); 
    
            // Instanciar HistorialEmp y setterar datos
            $historial = new HistorialEmp($this->db);
            $historial->setIdEmp($idEmpleado);
            $historial->setIdCategoria($categoria_id);
            $historial->setFechaInicio($fecha_inicio);
            $historial->setEstadoEmp($estado_emp); 
            
            $pass= '1234';
            //crear automaticamente usuario y contrasenia
            //cifrar contraseña
            $pass_cifrado =password_hash($pass,PASSWORD_DEFAULT,array("cost"=>8));
            $sql = "INSERT INTO credenciales (idempleados, usuario, password) VALUES (:id, :email_emp, :contrasena)";
            $ps = $this->db->prepare($sql);
            $ps->bindParam(':id', $idEmpleado);
            $ps->bindParam(':email_emp', $email);
            $ps->bindParam(':contrasena', $pass_cifrado);
            
            $result = $ps->execute();
            // Llamar al método para dar de alta el historial
            if (!$historial->altaHistorial() && !$result) {
                throw new Exception("Error al insertar el historial del empleado.");
            }
    
            // Confirmar la transacción
            $this->db->commit();
            return true;
    
        } catch (Exception $e) {
            // Si ocurre algún error, se deshace la transacción
            $this->db->rollBack();
            echo "Error al crear empleado e historial: " . $e->getMessage();
            return false;
        }
    }
    public function leerEmpleados($estado = null) {
        try {

            $query = "SELECT e.idempleados, e.dni_empleado, e.nom_empleado, e.ape_empleado, e.tel_empleado, e.email_empleado, e.idlocal, h.fecha_inicio_puesto,h.fecha_fin_puesto, h.idcategorias_empleados, c.tipo_empleado, h.descripcion_cambio
            FROM empleados AS e
            INNER JOIN historial_empleados AS h 
                ON e.idempleados = h.idempleados
            INNER JOIN categorias_empleados AS c 
                ON h.idcategorias_empleados = c.idcategorias_empleados
            WHERE h.fecha_inicio_puesto = (
                SELECT MAX(h2.fecha_inicio_puesto)
                FROM historial_empleados AS h2
                WHERE h2.idempleados = e.idempleados
            ) AND h.estado_empleado = :estado ORDER BY e.idempleados";
                    $ps = $this->db->prepare($query);
                    $estado = $estado ?? 1;
                    $ps->bindParam(':estado', $estado, PDO::PARAM_INT);

                    $ps->execute();
                    $empleados = [];
            
                    while ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                        // Instanciar las clases
                        $empleado = new Empleados($this->db);
                        $historial = new HistorialEmp($this->db);
                        $categoria = new CategoriasEmp($this->db);
                        
                        // Asignar los datos del empleado
                        $empleado->setIdEmp($fila['idempleados']);
                        $empleado->setDniEmp($fila['dni_empleado']);
                        $empleado->setNomEmp($fila['nom_empleado']);
                        $empleado->setApeEmp($fila['ape_empleado']);
                        $empleado->setTelEmp($fila['tel_empleado']);
                        $empleado->setEmailEmp($fila['email_empleado']);
                        $empleado->setIdLocal($fila['idlocal']);
                        
                        $historial->setFechaInicio($fila['fecha_inicio_puesto']);
                        $historial->setFechaFin($fila['fecha_fin_puesto']);
                        $historial->setDescripcion($fila['descripcion_cambio']);
                        $historial->setIdCategoria($fila['idcategorias_empleados']);
                        
                        $categoria->setTipoEmp($fila['tipo_empleado']);
                        
                        $empleados[] = [
                            'empleado' => $empleado,
                            'historial' => $historial,
                            'categoria' => $categoria
                        ];
                    }
            
                    return $empleados;
            
                } catch (Exception $e) {
                    echo "Error al obtener los empleados: " . $e->getMessage();
                    return [];
                }
    }
            

    public function obtenerUnEmp($id) {
        try {
            $query = "SELECT * FROM empleados WHERE idempleados = :id_empleado"; 
                
            $ps = $this->db->prepare($query);
            $ps->bindParam(':id_empleado', $id, PDO::PARAM_INT);
            $ps->execute();
        
            if ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $empleado = new Empleados($this->db);
                
                $empleado->setIdEmp($fila['idempleados']);
                $empleado->setDniEmp($fila['dni_empleado']);
                $empleado->setNomEmp($fila['nom_empleado']);
                $empleado->setApeEmp($fila['ape_empleado']);
                $empleado->setTelEmp($fila['tel_empleado']);
                $empleado->setEmailEmp($fila['email_empleado']);
                $empleado->setDirEmp($fila['dir_empleado']);
                $empleado->setIdLocal($fila['idlocal']);
                
                
                return $empleado; 
                
            } else {
                return "<h2>Ups! no se han encontrado datos</h2>";
            }
        
        } catch (Exception $e) {
            echo "Error al obtener el Empleado: " . $e->getMessage();
            return null;
        }
    }
    public function obtenerUnEmpDni($dni) {
        try {
            $query = "SELECT * FROM empleados WHERE dni_empleado = :dni"; 
                
            $ps = $this->db->prepare($query);
            $ps->bindParam(':dni', $dni);
            $ps->execute();
        
            if ($fila = $ps->fetch(PDO::FETCH_ASSOC)) {
                $empleado = new Empleados($this->db);
                
                $empleado->setIdEmp($fila['idempleados']);
                $empleado->setDniEmp($fila['dni_empleado']);
                $empleado->setNomEmp($fila['nom_empleado']);
                $empleado->setApeEmp($fila['ape_empleado']);
                $empleado->setTelEmp($fila['tel_empleado']);
                $empleado->setEmailEmp($fila['email_empleado']);
                $empleado->setDirEmp($fila['dir_empleado']);
                $empleado->setIdLocal($fila['idlocal']);
                
                
                return $empleado; 
                
            } else {
                return "<h2>Ups! no se han encontrado datos</h2>";
            }
        
        } catch (Exception $e) {
            echo "Error al obtener el Empleado: " . $e->getMessage();
            return null;
        }
    }

    
    public function modificarEmp() {
        try {
            $id = $this->getIdEmp();  // Obtener el ID del empleado antes de verificar el DNI
            $dni = $this->getDniEmp();
    
            // Verificar si el DNI es único excluyendo al empleado actual
            if (!$this->esDniUnico($dni, $id)) {
                echo "<h2>Error: El DNI ya está en uso por otro Empleado. </h2>";
                echo "<h3>No puede haber dos DNI iguales.</h3>";
                return false;
            }
    
            // Si el DNI no está en uso, se puede actualizar
            $query = "UPDATE empleados SET dni_empleado = :dni_emp, nom_empleado = :nom_emp, ape_empleado= :ape_emp, 
                      tel_empleado = :tel_emp, email_empleado = :email_emp, dir_empleado = :dir_emp, idlocal = :id_local
                      WHERE idempleados = :id";
    
            $nombre = $this->getNomEmp();
            $apellido = $this->getApeEmp();
            $telefono = $this->getTelEmp();
            $email = $this->getEmailEmp();
            $direccion = $this->getDirEmp();
            $local = $this->getIdLocal();

            $resultado = $this->bindParamQuerys($query, $id, $dni, $nombre, $apellido, $telefono, $email, $direccion, $local);
            return $resultado;
    
        } catch (Exception $e) {
            echo "Error al modificar el Empleado: " . $e->getMessage();
            return false;
        }
    }
    //getters 
    public function getIdEmp(){
        return $this->id_emp;
    }
    public function getDniEmp(){
        return $this->dni_emp;
    }
    public function getNomEmp(){
        return ucwords($this->nom_emp);
    }
    public function getApeEmp(){
        return ucwords($this->ape_emp);
    }
    public function getTelEmp(){
        return $this->tel_emp;
    }
    public function getEmailEmp(){
        return $this->email_emp;
    }
    public function getDirEmp(){
        return ucwords($this->dir_emp);
    }
    public function getIdLocal(){
        return ucwords($this->id_local);
    }

    //setters 
    public function setIdEmp($id_emp){
        $this->id_emp = $id_emp;
    }
    public function setDniEmp($dni_emp){
        $this->dni_emp = $dni_emp;
    }
    public function setNomEmp($nom_emp){
        $this->nom_emp = $nom_emp;
    }
    public function setApeEmp($ape_emp){
        $this->ape_emp = $ape_emp;
    }
    public function setTelEmp($tel_emp){
        $this->tel_emp = $tel_emp;
    }
    public function setEmailEmp($email_emp){
        $this->email_emp = $email_emp;
    }
    public function setDirEmp($dir_emp){
        $this->dir_emp = $dir_emp;
    }
    public function setIdLocal($id_local){
        $this->id_local = $id_local;
    }

    public function columnaExiste($tabla, $columna) {
        $stmt = $this->db->prepare("SHOW COLUMNS FROM $tabla LIKE :columna");
        $stmt->execute(['columna' => $columna]);
        return $stmt->rowCount() > 0;
    }

    public function agregarColumnaSiNoExiste($tabla, $columna, $definicion) {
        if (!$this->columnaExiste($tabla, $columna)) { 
            $sql = "ALTER TABLE $tabla ADD $columna $definicion";
            $this->db->exec($sql);
        }
    }
    
}

?>