<?php

namespace App\Services;

use App\Utils\LoggerFuncs;
use App\Factories\LoggerFactory;
use App\Validation\ValidationError;
use App\Validation\ValidationResult;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Selective\Config\Configuration;

class MailService {

  use LoggerFuncs;

  public function __construct(LoggerFactory $loggerFactory, Configuration $config) {
    $this->logger = $loggerFactory->createInstance("MailService");
    $this->emailSettings = $config->getArray("email");
  }

  public function sendEmail(string $sender, array $recipients, string $subject, string $content) {
    $mail = new PHPMailer(true);
    $this->logger->info('smtp host: ' . $this->emailSettings['host'] . ': ' . $this->emailSettings['port']);
    try {
      $mail->isSMTP();
      $mail->SMTPDebug  = 0;
      $mail->SMTPAuth   = true;
      $mail->SMTPSecure = 'ssl';
      $mail->Host       = $this->emailSettings['host'];
      $mail->Port       = $this->emailSettings['port'];
      $mail->Username   = $this->emailSettings['user'];
      $mail->Password   = $this->emailSettings['password'];

      //Recipients
      $mail->setFrom($sender);
      foreach ($recipients as $recipient) {
        $mail->addAddress($recipient);
      }

      //Content
      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body    = $content;

      $mail->send();
      return new ValidationResult(true);
    } catch (Exception $e) {
      return new ValidationError('email could not be sent: ' . $mail->ErrorInfo);
    }
  }

}
