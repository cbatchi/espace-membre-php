<?php
  require './includes/bootstrap.php';
    $app = App::getInstance();

    // accès account interdit sans connexion au préalable
    $app->auth->onlyLogged();

    // Mise à jour du mot de passe
    $app->auth->userUpdatePassword();
    
    // Récuperations des erreurs si elles existent
    $errors = $app->auth->getErrors();
  require './includes/header.php';
?>

<h1>Bonjour <?= $app->sessions->getSessionKey('auth')->username; ?></h1>

<?php if (!empty($errors) && isset($errors)) : ?>
  <div class="alert alert-danger">
    <p>Ce formulaire contient des erreurs</p>
    <ul>
      <?php foreach ($errors as $error) : ?>
        <li><?= $error ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>


<div class="container">
  <form action="" method="POST">
    <div class="form-group">
      <label for="password">Mot de passe</label>
      <input type="password" name="password" id="password" class="form-control" />
    </div>
    <div class="form-group">
      <label for="password-confirm">Confirmation de mot de passe</label>
      <input type="password" name="password-confirm" id="password-confirm" class="form-control" />
    </div>
    <button type="submit" class="btn btn-primary">Changer votre mot de passe</button>
  </form>
</div>

<?php require './includes/footer.php'; ?>