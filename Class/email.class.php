<?php
    //
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;
    //
    require_once('Exception.php');
    require_once('PHPMailer.php');
    require_once('SMTP.php');
    require_once("parametros.class.php");
	require_once("util.class.php");
    
	class Email {
		private $db;
		private $util;

		function __construct($db){
			$this->db = $db;
			$this->util = new Util();
			$this->parametros = new Parametros($db);
		}

		public function enviaEmailSimples($emailTo, $assunto, $corpo){
            $emailFrom = $this->parametros->buscaValor("sistema: email usado para contato do sistema");
            $emailSenha = $this->parametros->buscaValor("sistema: senha do email usado para contato do sistema");
            $smtpHost = $this->parametros->buscaValor("sistema: host smtp");
            //
            //Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(false);

            try {
                //Server settings
                $mail->SMTPDebug = SMTP::DEBUG_OFF ;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = $smtpHost;                              //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = $emailFrom;                             //SMTP username
                $mail->Password   = $emailSenha;                            //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $mail->setFrom($emailFrom, 'Sistema');
                //
                // Separa por ponto e virgula para incluir varios emails
                //
                $emails = explode(";", $emailTo);
                foreach($emails as $email){
                    if($email != ''){
                        $mail->addAddress($email);     //Add a recipient
                    }
                }

                //Attachments
                // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = $assunto;
                $mail->Body    = $corpo;

                $mail->send();
                // echo 'Email Enviado';
            } catch (Exception $e) {
                // echo "Erro no envio de email: {$mail->ErrorInfo}";
            }
		}
	}


?>
