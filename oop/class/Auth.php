<?php

class Auth {

  /**
   * @var array $errors;
   */
  private array $errors=[];

  /**
   * @var array $data;
   */
  private array $data;

  /**
   * @var Database $connection;
   */
  public Database $connection;

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
  public function __construct ($connection, $helper, $sessions) {
    $this->data = $this->requestType();
    $this->connection = $connection;
    $this->helper = $helper;
    $this->sessions = $sessions;
  }

  /**
   * fieldAndPatterns
   *
   * @return array
   */
  private function patterns(): array
  {
    return [
      '/^[a-zA-Z0-9_]+$/',
      '/^[a-z0-9][-a-z0-9._]+@([-a-z0-9]+[.])+[a-z]{2,5}$/',
      '/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/'
    ];
  }
  
  /**
   * fields
   *
   * @return array
   */
  private function fields(): array
  {
    $fields=[];
    foreach($this->errorMessages() as $key => $value) {
      array_push($fields, $key);
    }
    return $fields;
  }

  /**
   * errorMessages
   *
   * @return array
   */
  private function errorMessages(): array
  {
    return [
      'username' => [
        'Votre pseudo n\'est pas valide (Alphanumérique)',
        'Ce pseudo est deja pris'
      ],
      'email' => [
        'Le format de cet email n\'est pas valide',
        'Cet email est déjà associé à un compte'
      ],
      'password' => [
        'Votre mot de passe est incorrect ou ne correspondent pas'
      ]
    ];
  }

  /**
   * requestType
   *
   * @return array
   */
  private function requestType(): array
  {
    switch (strtolower($_SERVER['REQUEST_METHOD'])) {
      case 'get':
        return $_GET;
      break;
      case 'post':
        return $_POST;
      break;
    }
  }
  
  /**
   * checkFieldDatas
   *
   * @return void
   */
  public function checkFieldDatas () {
    foreach ($this->fields() as $key => $value) {
      if ($value == 'password') {
        if (
          empty($this->data[$value]) || !preg_match($this->patterns()[$key], $this->data[$value]) ||
          $this->data[$value] !== $this->data[$value . '-confirm']
        ) {
          $this->errors[$value] = $this->errorMessages()[$value][0];
        }
      } else {
        if (empty($this->data[$value]) || !preg_match($this->patterns()[$key], $this->data[$value])) {
          $this->errors[$value] = $this->errorMessages()[$value][0];
        } else {
          $this->checkIfAlreadyUser($value);
        }
      }
    }
  }

  /**
   * checkIfUserExist
   *
   * @return void
   */
  private function checkIfAlreadyUser($field)
  {
    $user = $this->connection->query("SELECT id FROM users WHERE $field=:$field", ["$field" => $this->data[$field]]);
    $user = $user->fetch();
    if ($user) $this->errors[$field] = $this->errorMessages()[$field][1];
  }

  /**
   * userRegister
   *
   * @return void
   */
  public function userRegister()
  {
    if (!$this->checkDataExist()) {
      // Verifie que les données existent
      $this->checkFieldDatas();
      // Verifie que le tableau d'erreurs est bien vide
      if($this->checkErrorExist()) {
        $token = $this->helper->str_random(60);
        $fields = $this->fields();
        array_push($fields, 'confirmation_token');
        // Appel la db connection et fais une nouvelle insertion
        $this->connection->query("INSERT INTO users (" . implode(', ', $fields).") VALUES (?,?,?,?)",
          [
            trim(htmlspecialchars($this->data[$fields[0]])),
            trim(htmlspecialchars($this->data[$fields[1]])),
            trim(htmlspecialchars($this->helper->hashPassword($this->data[$fields[2]]))),
            trim(htmlspecialchars($token))
          ]
        );
        // Envoi un mail de confirmation à l'utilisateur
        $this->helper->mailService([
          'userId' => $this->connection->lastInsertId(),
          'token' => $token,
          'to' => trim(htmlspecialchars($this->data[$fields[1]])),
          'filename' => 'confirm',
          'subject' => 'Confirmation de votre compte',
          'message' => 'Afin de confirmer votre compte, merci de cliquer sur ce lien'
        ]);
        // Affiche un message de retour d'operation puis redirige vers la page login
        $this->sessions->setSessionFlashMessages('success', 'Un mail de confirmation vous a été envoyé');
        $this->sessions->redirectTo('login');
      }
    }
  }

  
  /**
   * userLogin
   *
   * @return void
   */
  public function userLogin () {
    // $this->autoReconnectFromCookie();
    if (!$this->checkDataExist()) {
      if ($this->checkErrorExist()) {
        $user = $this->connection->query("SELECT * FROM users WHERE (username=:username OR email=:username) AND confirmed_at IS NOT NULL", 
        ['username' => $this->data['username']])->fetch();

        if ($user && password_verify($this->data['password'], $user->password)) {
          $this->sessions->setSessionKey('auth', $user);
          $this->sessions->setSessionFlashMessages('success', 'Vous êtes maintenant connécté');

          if ($this->data['remember']) {
            $remember_token = $this->helper->str_random(250);
            $this->connection->query("UPDATE users SET remember_token=:remember_token WHERE id=:id", 
            ['remember_token' => $remember_token, 'id' => $user->id]);

            $this->sessions->setCookie(
              'remember',
              $user->id . '//' . $remember_token . sha1($user->id . 'localdev'),
              strtotime('+1 day'),
              '/'
            );
          }
          $this->sessions->redirectTo('account');
        } else {
          $this->sessions->setSessionFlashMessages('danger', 'Identifiants (username or email / password) incorrects');
        }
      }
    }
  }
  
  /**
   * autoReconnectFromCookie
   *
   * @return void
   */
  private function autoReconnectFromCookie () {
    if (isset($_COOKIE['remember']) && !isset($_SESSION['auth'])) {
      // get token store inside cookie
      $remember_token = $_COOKIE['remember'];
      // Get the first item of after explode
      $userId = explode('//', $remember_token)[0];
      $user = $this->connection->query("SELECT * FROM users WHERE id=:id", ['id' => $userId])->fetch();
      if ($user) {
        $expected = $userId . '//' . $user->remember_token . sha1($userId . 'localdev');
        if ($expected == $remember_token) {
          $this->sessions->setSessionKey('auth', $user);
          $this->sessions->setCookie('remember', $remember_token, strtotime('+1 day'));
        } else {
          $this->sessions->setCookie('remember', NULL, strtotime('-1 day'));
        }
      } else {
        $this->sessions->setCookie('remember', NULL, strtotime('-1 day'));
      }
    }
  }
  
  /**
   * userUpdatePassword
   *
   * @return void
   */
  public function userUpdatePassword () {
    if (!$this->checkDataExist()) {
      if (empty($this->data['password']) ||!preg_match($this->patterns()[2],$this->data['password']) || 
      $this->data['password'] != $this->data['password-confirm']) {
        $this->errors['password'] = 'Mot de passes incorrects ou ne correspondent pas';
      }
      if ($this->checkErrorExist()) {
        $userId = $this->sessions->getSessionKey('auth')->id;
        $passwordhash = $this->helper->hashPassword($this->data['password']);

        $this->connection->query("UPDATE users SET password=:password WHERE id=:id", [
          'password' => $passwordhash,
          'id' => $userId
        ]);
        $this->sessions->setSessionFlashMessages('success', 'Votre mot de passe a été mis à jour, merci de vous reconnecter');
        $this->sessions->redirectTo('login');
      }
    }
  }

  public function onlyLogged () {
    if (!$this->sessions->getSessionKey('auth')) {
      $this->sessions->setSessionFlashMessages('danger', 'Accès refuser, vous devez vous connecter avant');
      $this->sessions->redirectTo('login');
    }
  }
  
  /**
   * userRecoveryPassword
   *
   * @return void
   */
  public function userRecoveryPassword () {
    if (!$this->checkDataExist()) {
      if (empty($this->data['email']) || !preg_match($this->patterns()[1], $this->data['email']) ||
          $this->data['email'] != $this->data['email-confirm']) {
        $this->errors['email'] = 'Le format de cet email n\'est pas valide ou ne correspondent pas';
      }

      if ($this->checkErrorExist()) {
        $user = $this->connection->query("SELECT * FROM users WHERE email=:email AND confirmed_at IS NOT NULL", 
        ['email' => $this->data['email']])->fetch();

        if ($user) {
          $reset_token = $this->helper->str_random(60);
          $this->connection->query("UPDATE users SET reset_token=:reset_token, reset_at=NOW() WHERE id=:id", 
            ['reset_token' => $reset_token, 'id' => $user->id]
          );

          $this->sessions->setSessionFlashMessages('success', 'Les instructions de recuperation de mots de passe vous ont été envoyé');
          $this->helper->mailService([
            'filename' => 'reset',
            'userId' => $user->id,
            'token' => $reset_token,
            'to' => $this->data['email'],
            'subject' => 'Rénitialisation de votre mot de passe',
            'message' => 'Afin de renitialiser votre mot de passe, merci de cliquer sur ce lien'

          ]);
          $this->sessions->redirectTo('login');
        } else {
          $this->sessions->setSessionFlashMessages('danger', 'Aucun  compte n\' est associé à cet email');
        }
      }
    }
  }

  /**
   * userResetPassword
   *
   * @return void
   */
  public function userResetPassword () {
    $userId = $_GET['userId'];
    $token = $_GET['token'];
    if (!isset($userId, $token)) $this->sessions->redirectTo('login');

    $user = $this->connection->query("SELECT * FROM users WHERE id=:id 
      AND reset_token IS NOT NULL
      AND reset_token=:reset_token 
      AND reset_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE)
    ", ['id' => $userId, 'reset_token' => $token])->fetch();

    if (!$this->checkDataExist() && $user) {
      if (empty($this->data['password']) || !preg_match($this->patterns()[2], $this->data['password']) ||
        $this->data['password'] !== $this->data['password-confirm']
      ) {
        $this->errors['password'] = 'Ce formulaire est soit vide / les champs ne correspondent pas';
      }
      if ($this->checkErrorExist()) {
        $passHash = $this->helper->hashPassword($this->data['password']);
        $this->connection->query('UPDATE users SET password=:password, reset_at=NULL, reset_token=NULL WHERE id=:id',
          [
            'password' => $passHash,
            'id' => $user->id
          ]
        );
        $this->sessions->setSessionFlashMessages('success', 'Votre mot de passe a été renitialiser, merci de vous connecter');
        $this->sessions->redirectTo('login');
      }
    } else {
      $this->sessions->setSessionFlashMessages('danger', 'Ce token n\'est plus valide');
      $this->sessions->redirectTo('login');
    }
  }
  

  public function getFields () {
    return $this->fields();
  }
  
  /**
   * getErrors
   *
   * @return array
   */
  public function getErrors (): array {
    return $this->errors;
  }
  
  /**
   * checkErrorExist
   *
   * @return void
   */
  public function checkErrorExist() {
    return empty($this->errors);
  }
  
  /**
   * getDatas
   *
   * @return array
   */
  public function getDatas (): array {
    return $this->data;
  }

  /**
   * getDatas
   *
   * @return array
   */
  public function checkDataExist(): bool
  {
    return empty($this->data);
  }
}