<?php

class Database {
  private static $instance = null;
  
  private $pdo;
  
  /**
   * __construct
   *
   * @return void
   */
  public function __construct()
  {
    $this->pdoConnection();
  }
  
  
  /**
   * getInstance
   *
   * @return self
   */
  public static function getInstance () {
    if (is_null(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  /**
   * pdoConnection
   *
   * @return PDO
   */
  private function pdoConnection (): PDO {
    try {
      extract($this->pdoSettings());
      $pdo = $this->pdo = new PDO($dsn, $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
      ]);
      return $this->pdo = $pdo;
    } catch (PDOException $e) {
      die('Connection to database failed '. $e->getMessage());
    }
  }
  
  /**
   * pdoSettings
   *
   * @return array
   */
  public function pdoSettings (): array {
    return [
      'dsn' => 'mysql:host=localhost;dbname=user_management',
      'dbuser' => '', // Votre nom d'utilisateur 
      'dbpass' => '' // Votre mot de passe 
    ];
  }
  
  /**
   * query
   *
   * @param  mixed $sql
   * @param  mixed $params
   * @return PDOStatement
   */
  public function query ($sql, $params): PDOStatement {
    $req = $this->pdo->prepare($sql);
    $req->execute($params);
    return $req;
  }

  public function lastInsertId () {
    return $this->pdo->lastInsertId();
  }
}