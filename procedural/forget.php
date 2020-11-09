<?php
    require './scripts/recoveryPassword.php';
    require './includes/header.php';
?>

<?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
        <p>Ce formulaire contient des erreurs</p>
        <ul>
            <?php foreach($errors as $error): ?>
            <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<h1>Formulaire de récupération</h1>

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