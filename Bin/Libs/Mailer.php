<?php

/**
 * Envio de e-mails via PHPMailer (SMTP).
 * Os arquivos do PHPMailer ficam em Bin/Libs/PHPMailer/ e são incluídos
 * manualmente aqui porque usam namespace, e o autoloader deste projeto
 * (ver AME/index.php) só resolve classes sem namespace.
 *
 * Configure as credenciais SMTP em AME/Conf/Config.php (chaves SMTP_*).
 */
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Mailer {

    /**
     * @param string $to      E-mail do destinatário
     * @param string $subject Assunto
     * @param string $body    Corpo em HTML
     * @return bool           true se enviado, false em caso de erro
     */
    public static function send($to, $subject, $body) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = SMTP_SECURE; // 'tls' ou 'ssl'
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (PHPMailerException $e) {
            error_log('Falha ao enviar e-mail: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
