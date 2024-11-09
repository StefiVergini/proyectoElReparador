<?php

  require_once("../conexionPDO.php");

  if(!isset($_POST["modificar"])){
  $id = $_GET['id'];
  $documento = $_GET['doc'];
  $nombre = $_GET['nom'];
  $apellido = $_GET['ape'];
  $contacto = $_GET['contac'];
  $email = $_GET['correo'];
  $domicilio = $_GET['dom'];
  $estado = $_GET['est'];

  }else{
  $id = $_POST['id'];
  $documento = $_POST['dni'];
  $nombre = $_POST['name'];
  $apellido = $_POST['apellido'];
  $contacto = $_POST['contacto'];
  $email = $_POST['email'];
  $domicilio = $_POST['domicilio'];
  $estado = $_POST['estado'];

  

  $sql = "UPDATE clientes SET dni_cliente=:documento, nom_cliente=:nombre, ape_cliente=:apellido, tel_cliente=:contacto, dir_cliente=:domicilio, email_cliente=:email, estado_cliente=:estado WHERE idclientes=:id";
  $resultado=$base->prepare($sql);
  $resultado->execute(array(":id"=>$id,":documento"=>$documento, ":nombre"=>$nombre, ":apellido"=>$apellido,
":contacto"=>$contacto, ":domicilio"=>$domicilio, ":email"=>$email, ":estado"=>$estado));
  header("Location:leer_db.php");
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Modificar Cliente</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="style.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
  </head>
  <body>
    <?php
      require '../header.php';
    ?>
    
    <h4>
        Modificar cliente
    </h4>
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <label class="label">Cod. Cliente:</label>
        <input class="input_id" type="number" name="id" id="" readonly="readonly" value=<?php echo $id; ?>>
        <br> 
        <label class="label" for="dni">DNI / CUIT:</label>
        <input class="input" type="number" name="dni" id="" value=<?php echo $documento; ?> required>
        <label class="label" for="nombre">Nombre / Razón social:</label>
        <input class="input" type="text" name="name" id="" value="<?php echo $nombre; ?>" required>
        <label class="label" for="">Apellido:</label>
        <input class="input" type="text" name="apellido" id="" value="<?php echo $apellido; ?>" required>
        <br>
        <br>
        <label class="label" for="telefono">Teléfono:</label>
        <input class="label" class="input" type="number" name="contacto" id="" value=<?php echo $contacto; ?> required>
        <label class="label" for="mail">Mail:</label>
        <input class="input" class="input" type="email" name="email" id="" value=<?php echo $email; ?>>
        <label class="label" for="domicilio">Dirección:</label> 
        <input class="input" type="text" name="domicilio" id="" value="<?php echo $domicilio;?>">
        <br>
        <br>
        <label class="label" for="estado">Estado:</label>
        <select class="input" type="number" name="estado" id="" value=<?php echo $estado; ?> required>
        <option value="1">1</option>
          <option value="0">0</option>
        </select>
        <div class="botones">
        <button class="btn" style="margin-right: 5px" type="submit" value="Guardar" name="modificar">Modificar</button>
        <button class="btn" style="margin-left: 5px"><a href="leer_db.php">Cancelar</a></button>
        </div>
    </form>

    <br>
    <br>
    <?php
      require '../footer.php';
    ?>
</body>
</html>