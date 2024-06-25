<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/Exception.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';

function send_verify_email(string $recp_email, string $verify_url): void
{
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'ikki.vts.su.ac.rs';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->Username = 'nda';
    $mail->Password = 'BesZKhwcizViY7s';
    $mail->setFrom('nda@nda.stud.vts.su.ac.rs', 'Subotica Rentals');
    $mail->addAddress($recp_email, '');

    $mail->Subject = "Verify your Subotica Rentals Account";
    $mail_template = file_get_contents('mail_templates/verify_account.html');
    $mail_html = str_replace(':VERIFY_URL', $verify_url, $mail_template);
    $mail->msgHTML($mail_html);
    $mail->AltBody = strip_tags($mail_html);

    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}

function send_advert_contact_email(string $sender_email, string $sender_name, string $recp_email, string $recp_name, string $adInfo, string $message): bool
{
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'ikki.vts.su.ac.rs';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->Username = 'nda';
    $mail->Password = 'BesZKhwcizViY7s';
    $mail->setFrom('nda@nda.stud.vts.su.ac.rs', 'Subotica Rentals');
    $mail->addAddress($recp_email, $recp_name);
    $mail->addReplyTo($sender_email, $sender_name);

    $mail->Subject = "$sender_name contacted you regarding your Ad on Subotica Rentals!";
    $mail_template = file_get_contents('mail_templates/advert_contact.html');
    $mail_html = str_replace(':SUBJECT', $mail->Subject, $mail_template);
    $mail_html = str_replace(':AD_INFO', $adInfo, $mail_html);
    $mail_html = str_replace(':MESSAGE', $message, $mail_html);
    $mail->msgHTML($mail_html);
    $mail->AltBody = strip_tags($mail_html);

    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
    return true;
}

function send_password_reset_email(string $recp_email, string $reset_url): bool
{
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'ikki.vts.su.ac.rs';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->Username = 'nda';
    $mail->Password = 'BesZKhwcizViY7s';
    $mail->setFrom('nda@nda.stud.vts.su.ac.rs', 'Subotica Rentals');
    $mail->addAddress($recp_email, '');

    $mail->Subject = "Reset your Subotica Rentals Password";
    $mail_template = file_get_contents('mail_templates/reset_password.html');
    $mail_html = str_replace(':RESET_URL', $reset_url, $mail_template);
    $mail->msgHTML($mail_html);
    $mail->AltBody = strip_tags($mail_html);

    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
    return true;
}

function send_tenant_email(string $sender_email, string $sender_name, string $recp_email, string $recp_name, string $adInfo): bool
{
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'ikki.vts.su.ac.rs';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->Username = 'nda';
    $mail->Password = 'BesZKhwcizViY7s';
    $mail->setFrom('nda@nda.stud.vts.su.ac.rs', 'Subotica Rentals');
    $mail->addAddress($recp_email, $recp_name);
    $mail->addReplyTo($sender_email, $sender_name);

    $mail->Subject = "$sender_name added you as a tenant on Subotica Rentals!";
    $mail_template = file_get_contents('mail_templates/tenant_contact.html');
    $mail_html = str_replace(':SUBJECT', $mail->Subject, $mail_template);
    $mail_html = str_replace(':AD_INFO', $adInfo, $mail_html);
    $mail->msgHTML($mail_html);
    $mail->AltBody = strip_tags($mail_html);

    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
    return true;
}
