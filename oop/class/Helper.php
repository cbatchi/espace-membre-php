<?php

class Helper {
  
  /**
   * str_random
   *
   * @param  mixed $length
   * @return string
   */
  public function str_random(int $length): string
  {
    $alphabet = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN0123456789';
    return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
  }

  /**
   * hashPassword
   *
   * @param  mixed $password
   * @param  mixed $algo
   * @return string|false|null
   */
  public function hashPassword ($password, $algo = PASSWORD_BCRYPT) {
    return password_hash($password, $algo);
  }
  
  /**
   * mailService
   *
   * @param  mixed $to
   * @param  mixed $subject
   * @param  mixed $message
   * @param  array $options
   * @return void
   */
  public function mailService ($options=[]) {
    extract($options);
    $uri = "http://localhost:8000/$filename.php?userId=$userId&token=$token";
    $buttons = '<a href=' . $uri . '>cliquez</a>';
    mail($to, $subject, $message . $buttons);
  }
}