<?php

/**
 * Mailer — thin wrapper around PHPMailer using SMTP credentials from .env
 *
 * Usage:
 *   Mailer::send('to@example.com', 'Subject', '<p>HTML body</p>');
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailerException;

require_once ROOT . '/vendor/phpmailer/PHPMailer.php';
require_once ROOT . '/vendor/phpmailer/SMTP.php';
require_once ROOT . '/vendor/phpmailer/Exception.php';

class Mailer
{
    /**
     * Send an email.
     *
     * @param  string      $toEmail
     * @param  string      $subject
     * @param  string      $htmlBody   HTML content
     * @param  string|null $toName     Optional display name for recipient
     * @return bool                    true on success, false on failure
     */
    public static function send(string $toEmail, string $subject, string $htmlBody, string $toName = ''): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = env('SMTP_HOST', '');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('SMTP_USER', '');
            $mail->Password   = env('SMTP_PASS', '');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int) env('SMTP_PORT', 587);

            $mail->setFrom(
                env('SMTP_FROM_EMAIL', env('SMTP_USER', '')),
                env('SMTP_FROM_NAME', 'NewPath')
            );
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            $mail->send();
            return true;

        } catch (MailerException $e) {
            error_log('Mailer error: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
