<?php

class App {

  /**
   * @var null $instance;
   */
  private static $instance = null;

  /**
   * @var Database $connection;
   */
  public Database $connection;

  /**
   * @var Auth $auth;
   */
  public Auth $auth;

  /**
   * @var Helper $helper;
   */
  public Helper $helper;

  /**
   * @var Sessions $sessions;
   */
  public Sessions $sessions;

    
  /**
   * __construct
   *
   * @return void
   */
  private function __construct () {
    $this->sessions = new Sessions();
    $this->connection = Database::getInstance();
    $this->helper = new Helper();
    $this->auth = new Auth($this->connection, $this->helper, $this->sessions);
  }
  
  /**
   * getInstance
   *
   * @return mixed
   */
  public static function getInstance (): self {
    if (is_null(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }


}