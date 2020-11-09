<?php
  require './includes/bootstrap.php';
    $app = App::getInstance();
    $app->auth->userResetPassword();
    $errors = $app->auth->getErrors();
  require './includes/header.php';
?>

<h1>Renitialisez votre mot de passe</h1>

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
    <label for="password">Mot de passe</label>
    <input type="password" name="password" id="password" class="form-control" />
  </div>

  <div class="form-group">
    <label for="password">Confirmez votre mot de passe</label>
    <input type="password" name="password-confirm" id="password" class="form-control" />
  </div>
  <button type="submit" class="btn btn-primary">Renitialiser</button>
</form>

<?php require './includes/footer.php'; ?>