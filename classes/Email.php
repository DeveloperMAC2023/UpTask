<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

Class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username ='3ae3ae3b1bf88f';
        $mail->Password = '0ce1f5b5a668a1';
         
        $mail->setFROM('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com','UpTask.com');
        $mail->Subject = 'Confirma tu cuenta';
        
        // Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has creado tu cuenta en UpTask, solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/confirmar?token=" . $this->token . "'>Confirmar Cuenta</a> </p>";
        $contenido .= "<p>Si tu no solicitaste una cuenta, puedes ignorar este mensaje</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;
                
        // Enviar mail
        $mail->send();

    }

    public function enviarInstrucciones() {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username ='3ae3ae3b1bf88f';
        $mail->Password = '0ce1f5b5a668a1';

        $mail->setFROM('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com','UpTask.com');
        $mail->Subject = 'Reestablece tu Password';
        
        // Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/reestablecer?token=" . $this->token . "'>Reestablecer Password</a> </p>";
        $contenido .= "<p>Si tu no solicitaste una cuenta, puedes ignorar este mensaje</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;
                
        // Enviar mail
        $mail->send();

    }


}