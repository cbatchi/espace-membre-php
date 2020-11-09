<?php
  require './includes/bootstrap.php';
    $app = App::getInstance();
    // Lance la recuperation de mot de passe
    $app->auth->userRecoveryPassword();
    // Recupère les erreurs si elles existent
    $errors = $app->auth->getErrors();
  require './includes/header.php';
?>

<h1>Formulaire de récupération</h1>

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

<form action="" method="POST">
  <div class="form-group">
    <label for="email">Email</label>
    <input type="text" name="email" id="email" class="form-control" />
  </div>

  <div class="form-group">
    <label for="email">Confirmez votre email</label>
    <input type="text" name="email-confirm" id="email" class="form-control" />
  </div>
  <button type="submit" class="btn btn-primary">Envoyer</button>
</form>

<?php require './includes/footer.php'; ?>