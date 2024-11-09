<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//required files
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

if (isset($_POST["enviar"])) {

    $mail = new PHPMailer(true);
  
      //Server settings
      $mail->isSMTP();                              //Send using SMTP
      $mail->Host       = 'smtp.gmail.com';       //Set the SMTP server to send through
      $mail->SMTPAuth   = true;             //Enable SMTP authentication
      $mail->Username   = 'elreparadorsrl@gmail.com';   //SMTP write your email
      $mail->Password   = 'cbgyroccxnlrcfsl';      //SMTP password
      $mail->SMTPSecure = 'ssl';            //Enable implicit SSL encryption
      $mail->Port       = 465;                                    
  
      //Recipients
      $mail->setFrom('elreparadorsrl@gmail.com', $_POST["nombre"]); // Sender Email and name
      $mail->addAddress($_POST["e-mail"]);     //Add a recipient email  
      $mail->addReplyTo($_POST["e-mail"], $_POST["nombre"]); // reply to sender email
  
      //Content
      $mail->isHTML(true);               //Set email format to HTML
      $mail->Subject = $_POST["asunto"];   // email subject headings
      $mail->Body    = $_POST["mensaje"]; //email message
  
      // Success sent message alert
      $mail->send();
      echo
      " 
      <script> 
       alert('Mensaje enviado correctamente!');
       document.location.href = 'mail.php';
      </script>
      ";
  }

?>