<?php

namespace Waddup\Utils;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Waddup\Config\Config;

class Mail
{
    protected PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->init();
    }

    public function init()
    {
        try {
            $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $this->mail->isSMTP();
            $this->mail->Host = Config::EMAIL('EMAIL_SMTP_HOST');
            $this->mail->SMTPAuth = true;
            $this->mail->Username = Config::EMAIL('EMAIL_NOREPLY');
            $this->mail->Password = Config::EMAIL('EMAIL_NOREPLY_PASS');
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mail->Port = 465;
            $this->mail->setFrom('nicko@waddup.com', 'Waddup');
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }

    /**
     * @throws Exception
     */public function send(string $to, string $subject, string $body, string $html): bool
{
        $mail = $this->mail;

        $mail->setFrom('nicko@waddup.com', 'Waddup');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html;
        $mail->AltBody = $body;

        return $mail->send();
    }
}