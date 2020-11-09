<?php

class Sessions {
  
  /**
   * __construct
   *
   * @return void
   */
  public function __construct()
  {
    $this->startSessionIfNotExist();
  }

  /**
   * setSession
   *
   * @param  mixed $key
   * @param  mixed $values
   * @return void
   */
  public function setSessionKey ($key, $values) {
    $_SESSION[$key] = $values;
  }
  
  /**
   * getSessionKey
   *
   * @param  mixed $key
   * @return mixed
   */
  public function getSessionKey ($key) {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
  }
  
  /**
   * setSessionFlashMessages
   *
   * @param  mixed $key
   * @param  mixed $message
   * @return void
   */
  public function setSessionFlashMessages ($key, $message): void {
    $_SESSION['flash'][$key] = $message;
  }
  
  /**
   * startSessionIfNotExist
   *
   * @return void
   */
  private function startSessionIfNotExist (): void {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }
  
  /**
   * unsetSessions
   *
   * @param  mixed $key
   * @return void
   */
  public function unsetSessions ($key) {
    unset($_SESSION[$key]);
  }
  
  /**
   * setCookie
   *
   * @param  mixed $key
   * @param  mixed $content
   * @param  mixed $duration
   * @return void
   */
  public function setCookie($key, $content, $duration, $path=false) {
    setcookie($key, $content, $duration, $path);
  }
  
  /**
   * redirectTo
   *
   * @param  mixed $target
   * @return void
   */
  public function redirectTo (string $target): void {
    header("Location: $target.php");
    exit();
  }

}