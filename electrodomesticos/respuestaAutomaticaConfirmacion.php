<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

        // Incluir PHPMailer
        require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/../PHPMailer/src/Exception.php';
        require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

function rtaAutomaticaConfirmacion($mailCli,$nomCli,$idRepa, $nomTipo, $marca, $modelo, $fechaFin){



    $mail = new PHPMailer(true);
    
    try {
        $nomTipo = strtoupper($nomTipo);
        $marca = strtoupper($marca);
        $modelo = strtoupper($modelo);
        // Configurar servidor SMTP
        $mail->isSMTP();                                 
        $mail->SMTPAuth   = true;   
        $mail->Host       = 'smtp.gmail.com';                       
        $mail->Username   = 'srlelreparador@gmail.com';  
        $mail->Password   = 'vxqwokxorbpdjnbn';  // contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //ENCRYPTION_SMTPS Puerto seria 465
        $mail->Port       = 587;
    
        $mail->setFrom('srlelreparador@gmail.com', 'EL REPARADOR SRL'); 
        $mail->addAddress(htmlspecialchars($mailCli)); 

        $logoPath = '../static/images/logo.png'; // Ruta al logo
        $cidLogo = 'logo_cid'; // Identificador único para el logo
        $mail->addEmbeddedImage($logoPath, $cidLogo);
    
        // Configurar contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Reparacion Confirmada';
        $mail->Body = "
                    <html>
                        <body style='background-color: #8497c5; font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 10px; border-radius: 10px;'>
                            <div style='display: flex; align-items: center;'>
                                <img src='cid:{$cidLogo}' alt='Logo' style='height: 180px; width: 180px; margin: 1px;'>
                                <h1 style='margin-top: 60px; margin-left: 5%; padding:20px; font-size:36px; color:black; text-decoration: underline;'>Respuesta Automática</h1>
                            </div>
                            <br>
                            <hr>
                            <h1 style='text-align:center; font-size:24px; color:black;'>Código de Reparación: {$idRepa}</h1>
                            <hr> 
                            <br>
                            <p style='font-weight: bold; font-size: 18px; margin: 15px 0;'>Estimado/a {$nomCli}:</p>  
                            <p style='text-align: center; font-weight: bold; font-size: 18px;'>Hemos recibido la Confirmación del Presupuesto de su Electrodoméstico: {$nomTipo} {$marca} {$modelo} </p>
                            <p style='text-align: center; font-weight: bold; font-size: 18px; margin: 15px 0;'>La fecha estimada de finalización de la Reparación es: {$fechaFin}</p>
                            <p style='text-align: center; font-weight: bold; font-size: 18px; margin: 15px 0;'>De todas formas, le enviaremos un correo cuando se encuentre reparado el electrodoméstico.</p>
                            <p style='text-align: center; font-weight: bold; font-size: 18px; margin: 15px 0;'>Recuerde que debe presentar el código de reparación.</p>
                            <br>
                            <hr>
                            <p style='text-align: center; font-weight: bold; font-size: 18px;'>Ante cualquier duda o consulta, estamos a su disposición.</p>
                            <p style='text-align: center; font-weight: bold; font-size: 18px;'>¡Gracias por confiar en nosotros!</p>
                            <br><br>
                            <p style= 'font-weight:bold; font-weight: bold; margin: 15px 0;'>Saludos,</p>
                            <p style= 'font-weight:bold; font-weight: bold; margin: 15px 0;'>EL REPARADOR</p>
                        </body>
                    </html>
            ";
    
            // Enviar el correo
            if ($mail->send()) {
                echo "Enviado";
            } else {
                echo "Error al enviar el correo";
            }
    
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$mail->ErrorInfo}";
        }
}

?>