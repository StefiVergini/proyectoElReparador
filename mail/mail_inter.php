<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Cargar las clases de PHPMailer
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

// Incluye mail.php usando la ruta correcta
include_once __DIR__ . '/mail.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../env.php';
loadEnv(__DIR__ . '/../.env');

$mail = new PHPMailer(true);
//$mail->SMTPDebug = 0; 2 para hacer debug.

if (isset($_POST["enviar"])) {
    //var_dump($_POST);
    //exit;
    try {
        
        $tipoCorreo = $_POST["tipo_correo"];
        if ($tipoCorreo == "Otros") {
            $email = trim($_POST["other_email"]);
            
            // Validación básica del formato
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Por favor ingrese una dirección de correo válida");
            }
        } else {
            $email = $_POST["email-select"];
            
            // Validación para selección de email
            if (empty($email)) {
                throw new Exception("Por favor seleccione un destinatario");
            }
        }

        //$email = isset($_POST["email"]) && !empty($_POST["email"]) ? $_POST["email"] : $_POST["email-select"];
        $electrodomesticoFull = isset($_POST['electrodomestico_full']) ? $_POST['electrodomestico_full'] : 'No especificado';
        // Configuración del servidor SMTP
        $mail->isSMTP();                                 
        $mail->SMTPAuth   = true;   
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';             
        $mail->Username = $_ENV['MAIL_USERNAME'];
        $mail->Password = $_ENV['MAIL_PASSWORD']; 

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //ENCRYPTION_SMTPS Puerto seria 465
        $mail->Port       = 587;

        // Configuración de los destinatarios
        $mail->setFrom('srlelreparador@gmail.com', 'EL REPARADOR SRL'); 
        $mail->addAddress(htmlspecialchars($email));  
        $mail->addReplyTo(htmlspecialchars($email), htmlspecialchars($_POST["nombre"])); 

        $logoPath = '../static/images/logo.png'; // Ruta al logo
        $cidLogo = 'logo_cid'; // Identificador único para el logo
        $mail->addEmbeddedImage($logoPath, $cidLogo);

        //$tipoCorreo = $_POST["tipo_correo"];
        $plantilla = "";
        $nomCli = htmlspecialchars($_POST["nombre_cli"]);
        $descReparacion = htmlspecialchars($_POST["descRe"]);
        $materiales = htmlspecialchars($_POST["materiales"]);
        $id_reparacion = htmlspecialchars($_POST["id_repa"]);
        $token = isset($_POST['token']) ? $_POST['token'] : '';
        $urlConfirm = "http://localhost/php/proyectoElReparador/electrodomesticos/respuestaPresupuesto.php?accion=confirmar&token=" . urlencode($token);
        $urlReject  = "http://localhost/php/proyectoElReparador/electrodomesticos/respuestaPresupuesto.php?accion=rechazar&token=" . urlencode($token);
        $monto = htmlspecialchars($_POST["monto"]);
        switch ($tipoCorreo) {
            case "Presupuesto":
                
                
                
                
                $plantilla = "            
                <html>
                    <body style='background-color: #8497c5; font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 10px; border-radius: 10px;'>
                        <div style='display: flex; align-items: center;'>
                            <img src='cid:{$cidLogo}' alt='Logo' style='height: 180px; width: 180px; margin: 1px;'>
                            <h1 style='margin-top: 60px; margin-left: 5%; padding:20px; font-size:36px; color:black; text-decoration: underline;'>Presupuesto</h1>
                        </div>
                        <br>
                        <hr>
                        <h1 style='text-align:center; font-size:24px; color:black;'>Código de Reparación: {$id_reparacion}</h1>
                        <hr> 
                        <br>
                        <p style='font-weight: bold; font-size: 18px; margin: 15px 0;'>Estimado/a {$nomCli}:</p> 
                        <p style='text-align: center; font-weight: bold; font-size: 18px;'>El presupuesto para la reparación de su electrodoméstico: {$electrodomesticoFull}</p>
                        <p style='text-align: center; font-weight: bold; font-size: 18px;'>Tiene un valor de: $ {$monto}.</p>
                        <br>
                        <hr>
                        <p style= 'font-weight:bold; text-align: center; font-size: 18px; margin: 15px 0;'>Materiales a utilizar: </p>
                        <p style= 'font-weight:bold; text-align: center; font-size: 18px; margin: 10px 0;'>{$materiales}</p>
                        <hr>
                        <p style= 'font-weight:bold; text-align: center; font-size: 18px; margin: 15px 0;'>Detalle de la Reparación que se hará: </p>
                        <p style= 'font-weight:bold; text-align: center; font-size: 18px; margin: 10px 0;'>{$descReparacion}</p>
                        <hr>
                        <br><br> 
                        <p style='text-align: center; font-weight: bold; font-size: 18px;'>Aguardamos la confirmación para continuar con el proceso.</p>
                        <br>
                        <p style='text-align: center;'>
                            <a href='" . $urlConfirm . "' style='cursor:pointer; display: inline-block; background-color: blue; color: white; padding: 10px 20px; text-decoration: none; margin-right: 10px; border: solid black 2px; border-radius: 5px;'>
                            Confirmar Presupuesto
                            </a>
                            <a href='" . $urlReject . "' style='cursor:pointer; display: inline-block; background-color: red; color: white; padding: 10px 20px; text-decoration: none; border: solid black 2px; border-radius: 5px;'>
                            Rechazar Presupuesto
                            </a>
                        </p>
                        <p style='text-align: center; font-weight: bold; font-size: 18px;'>¡Gracias por confiar en nosotros!</p>
                        <br><br>
                        <p style='font-size: 18px;  font-weight: bold; margin: 15px 0;'>Saludos,</p>
                        <p style='font-size: 18px;  font-weight: bold; margin: 15px 0;'>EL REPARADOR</p>
                    </body>
                </html>";
                break;
            case "Reparacion":
                $plantilla = "
                 <html>
                    <body style='background-color: #8497c5; font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 10px; border-radius: 10px;'>
                        <div style='display: flex; align-items: center;'>
                            <img src='cid:{$cidLogo}' alt='Logo' style='height: 180px; width: 180px; margin: 1px;'>
                            <h1 style='margin-top: 60px; margin-left: 5%; padding:20px; font-size:36px; color:black; text-decoration: underline;'>Reparación Finalizada</h1>
                        </div>
                        <br>
                        <hr>
                        <h1 style='text-align:center; font-size:24px; color:black;'>Código de Reparación: {$id_reparacion}</h1>
                        <hr> 
                        <br>
                        <p style='font-weight: bold; font-size: 18px; margin: 15px 0;'>Estimado/a {$nomCli}:</p>  
                        <p style='text-align: center; font-weight: bold; font-size: 18px;'>Nos complace informarle que la reparación de tu electrodoméstico: {$electrodomesticoFull} está finalizada.</p>
                        <p style='text-align: center; font-weight: bold; font-size: 18px; margin: 15px 0;'>Para retirar su Electrodoméstico deberá presentar el Código de la Reparación y abonar el Monto Final.</p>
                        <br>
                        <hr>
                        <p style= 'font-weight:bold; text-align: center; font-size: 18px; margin: 15px 0;'>Monto Final a Abonar: </p>
                        <p style= 'font-weight:bold; text-align: center; font-size: 18px; margin: 10px 0;'>$ {$monto}</p>
                        <hr>
                        <br><br> 
                        <p style='text-align: center; font-weight: bold; font-size: 18px; margin: 15px 0;'>Le recordamos que posee una garantía de 3 meses, una vez retirado el producto</p>
                        <br><br> 
                        <p style='text-align: center; font-weight: bold; font-size: 18px;'>¡Gracias por confiar en nosotros!</p>
                        <br><br>
                        <p style= 'font-weight:bold; font-weight: bold; margin: 15px 0;'>Saludos,</p>
                        <p style= 'font-weight:bold; font-weight: bold; margin: 15px 0;'>EL REPARADOR</p>
                    </body>
                </html>";
                break;
            case "Otros":
                $plantilla = "
                <html>
                    <body style='background-color: #8497c5; font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 10px; border-radius: 10px;'>
                        <div style='display: flex; align-items: center;'>
                            <img src='cid:{$cidLogo}' alt='Logo' style='height: 180px; width: 180px; margin: 1px;'>
                            <h1 style='margin-top: 60px; margin-left: 5%; padding:20px; font-size:36px; color:black; text-decoration: underline;'>Comunicado</h1>
                        </div>
                        <br><br>
                        <p style= 'font-size: 18px; margin: 15px 0;'>Hola, se comunica {$_POST["nombre"]} en nombre de EL REPARADOR.</p>
                        <p style= 'font-size: 18px; margin: 15px 0;'>Quería enviar el siguiente mensaje: </p>
                        <p style= 'font-weight:bold; text-align: center; font-size: 18px; margin: 10px 0;'>{$_POST["mensaje"]}</p>
                        <br><br>
                        <p style= 'font-size: 18px; font-weight:bold; font-weight: bold; margin: 15px 0;'>Saludos,</p>
                        <p style= 'font-size: 18px; font-weight:bold; font-weight: bold; margin: 15px 0;'>{$_POST["nombre"]} de EL REPARADOR</p>
                    </body>
                </html>";
                break;
        }

        $mail->isHTML(true);
        $mail->Subject = htmlspecialchars($_POST["asunto"]);
        $mail->Body    = $plantilla;

        if ($tipoCorreo === "Presupuesto" && $mail->send()) {
            echo "<script>alert('Presupuesto generado y enviado por correo.'); document.location.href = '../electrodomesticos/inicioElectro.php';</script>";
            exit;

        }else if($tipoCorreo === "Reparacion" && $mail->send()){
            echo "<script>alert('Reparación finalizada y enviada por correo.'); document.location.href = '../electrodomesticos/inicioElectro.php';</script>";
            exit;
        }else if($tipoCorreo === "Otros" && $mail->send()){
            echo "<script>alert('Mensaje enviado correctamente!'); document.location.href = 'mail.php';</script>";
            exit;
        }
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}
?>