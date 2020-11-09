<?php
    require './lib/lib.php';

    onlyLogged();

    require './scripts/updatePassword.php';
    require './includes/header.php';
?>


    <h1>Bonjour <?= $_SESSION['auth']->username; ?></h1>

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

    <div class="jumbotron">
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

    </div>


<?php require './includes/footer.php'; ?>