<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Mail</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="./mail.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <?php
      require '../header.php';
      //$idEmp = $_SESSION['id'];
    ?>
    <main>
        <h1>Mensajes</h1>
        <div>
            <form class='mail-form' action="./mail_inter.php" method='POST'>
                <input class='input' name='nombre' type="text" placeholder='Escriba su Nombre...' tabindex="1">
                <input class='input' name='e-mail' type="mail" tabindex="2" placeholder="ejemplo@elreparador.com">
                <input class='input' name='asunto' type="text" placeholder='Asunto' tabindex="3">
                <textarea name='mensaje' class='text-area' name="" id="" placeholder='Escriba su mensaje' tabindex="4"></textarea>

                <input class='btn' name='enviar' type="submit" value='Enviar'>
            </form>
        </div>
    </main>

    <?php
      require '../footer.php';
    ?>
</body>
</html>