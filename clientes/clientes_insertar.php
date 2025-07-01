<?php

    /*require '../conexionPDO.php';*/
 

  /*$cliente = $_GET['id'];

  try{
    $sql = "INSERT INTO clientes (idclientes, dni_cliente, nom_cliente, ape_cliente, tel_cliente, dir_cliente, email_cliente, estado_cliente) FROM clientes WHERE idclientes = $cliente";

    $resultado=$base->prepare($sql);
    $resultado -> execute();

    if($resultado){
        while($row = $resultado ->fetch(PDO::FETCH_ASSOC)){
            $idCliente = $row['idclientes'];
            $dniCliente = $row['dni_cliente'];
            $nomCliente = $row['nom_cliente'];
            $apeCliente = $row['ape_cliente'];
            $telCliente = $row['tel_cliente'];
            $dirCliente = $row['dir_cliente'];
            $emailCliente = $row['email_cliente'];
            $estadoCliente = $row['estado_cliente'];
        }
    }
}catch(Exception $e){
    echo "Línea del error: " . $e->getLine();
}finally{
    $base=null;
}*/
/*$documento = $_POST['documento'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$contacto = $_POST['contacto'];
$email = $_POST['email'];
$domicilio = $_POST['domicilio'];
$estado = $_POST['estado'];

$sql = $base->prepare("INSERT INTO clientes(dni_cliente, nom_cliente, ape_cliente, tel_cliente, 
dir_cliente, email_cliente, estado_cliente)VALUES (:documento, :nombre, :apellido :contacto, :domicilio, :email, :estado)");

$sql->bindParam(':documento',$documento);
$sql->bindParam(':nombre',$nombre);
$sql->bindParam(':apellido',$apellido);
$sql->bindParam(':contacto',$contacto);
$sql->bindParam(':email',$email);
$sql->bindParam(':domicilio',$domicilio);
$sql->bindParam(':estado',$estado);
if ($sql->execute()){
    echo "Cliente registrado exitosamento";
}else{
    echo "Error";
}*/


/*$documento = $_POST['documento'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$contacto = $_POST['contacto'];
$email = $_POST['email'];
$domicilio = $_POST['domicilio'];
$estado = $_POST['estado'];



$sql = "INSERT INTO clientes(dni_cliente, nom_cliente, ape_cliente, tel_cliente, 
dir_cliente, email_cliente, estado_cliente)VALUES ('$documento', '$nombre', '$apellido', '$contacto', '$domicilio', '$email', '$estado')";
$resultado=$base->prepare($sql);
if($base->query($sql)===true){
    echo "Registro guardado con éxito";
    echo "<br><a href='insertar.php'> Volver </a>";
}else{
    echo "error: " . $sql . $e->error;
};*/

  require("../conexionPDO.php");
  $consulta = "SELECT * FROM clientes";
  $resultado = $base->prepare("$consulta");
  $resultado->execute(array());
  $numreg=$resultado->rowCount();

  if(isset($_POST['insertar'])){
    $id="";
    $dni_cliente=$_POST["dni_cliente"];
    $nom_cliente=$_POST["nom_cliente"];
    $ape_cliente=$_POST["ape_cliente"];
    $tel_cliente=$_POST["tel_cliente"];
    $dir_cliente=$_POST["dir_cliente"];
    $email_cliente=$_POST["email_cliente"];
    $estado_cliente=$_POST["estado_cliente"];

    $sql = "INSERT INTO clientes(idclientes, dni_cliente, nom_cliente, ape_cliente, tel_cliente,
    dir_cliente, email_cliente, estado_cliente)VALUES ($id_clientes, '$dni_cliente', '$nom_cliente', '$ape_cliente', '$tel_cliente', '$dir_cliente', '$email_cliente', '$estado_cliente')";

    $resultado = $base->prepare("$consulta");
    $resultado->execute([':dni_cliente' => $dni_cliente, ':nom_cliente' => $nom_cliente, ':ape_cliente' => $ape_cliente, ':tel_cliente' => $tel_cliente, ':dir_cliente' => $dir_cliente, ':email_cliente' => $email_cliente, ':estado_cliente' => $estado_cliente]);

    header("Location:leer_db.php");

  }

?>
