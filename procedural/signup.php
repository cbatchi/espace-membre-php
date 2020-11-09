<?php
    require './scripts/signup.php';
    require './includes/header.php';
?>

<h1>S'inscrire</h1>

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

<form action="" method="POST">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="" />
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" class="form-control" placeholder="" />
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="" />
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label for="password-confirm">Confirmation de mot de passe</label>
                <input type="password" name="password-confirm" id="password-confirm" class="form-control" placeholder="" />
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">S'inscrire</button>
</form>

<?php require './includes/footer.php'; ?>
