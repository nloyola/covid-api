<?php

namespace App\Auth;

use App\Factories\LoggerFactory;
use App\Models\User;
use Selective\Config\Configuration;

class Auth {

  private $secret;

  /*
   * $secret comes from file config.ini
   */
  public function __construct(LoggerFactory $loggerFactory, Configuration $config) {
    $this->logger = $loggerFactory->createInstance("auth");
    $this->secret = $config->getString('auth_secret');
  }

  public function attempt($email, $password): bool {
    $user = User::where('email', $email)->first();
    if (!$user) {
      return false;
    }

    return password_verify($password, $user->password);
  }

  public function userId() {
    $session = new \SlimSession\Helper;
    return $session->appUserId;
  }

  public function createToken($user) {
    $uniqueValues = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
    $token = sha1($this->secret . uniqid(microtime() . $uniqueValues, true));

    $session = new \SlimSession\Helper;
    $session->appUserId = $user->id;
    $session->xsrfToken = (string) $token;
    $this->logger->debug('createToken: session xsrfToken: ' . $session->xsrfToken);
    return $session->xsrfToken;
  }

  public function removeToken() {
    $session = new \SlimSession\Helper;
    $session->appUserId = '';
    $session->xsrfToken = '';
  }

  public function validateToken($xsrfTokenHeader) {
    $session = new \SlimSession\Helper;

    $this->logger->debug('validateToken: xsrfTokenHeader: ' . $xsrfTokenHeader);
    $this->logger->debug('validateToken: session->xsrfToken: ' . $session->xsrfToken);

    return (isset($xsrfTokenHeader) && isset($session->xsrfToken) &&
            ($xsrfTokenHeader === $session->xsrfToken));
  }

}
