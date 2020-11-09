<?php 
  require './includes/bootstrap.php';

  $app = App::getInstance();
  extract($app->auth->getDatas());

  $user = $app->connection->query("SELECT * FROM users WHERE id=:id", ['id' => $userId])->fetch();

  if($user && $user->confirmation_token == $token) {
    $app->connection->query("UPDATE users SET confirmation_token=NULL, confirmed_at=NOW() WHERE id=:id", ['id' => $userId]);
    $app->sessions->setSessionFlashMessages('success', 'Votre compte a bien été confirmé');
    $app->sessions->setSessionKey('auth', $user);
    $app->sessions->redirectTo('account');
  } 
  else {
    $app->sessions->setSessionFlashMessages('danger', 'Ce token n\' est pas ou plus valide');
    $app->sessions->redirectTo('register');
  }