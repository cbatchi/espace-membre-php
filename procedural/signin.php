<?php
    require './scripts/signin.php';
    require './includes/header.php';
?>

<h1>Se connecter</h1>

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
    <div class="form-group">
        <label for="username">Username Or Email</label>
        <input type="text" name="username" id="username" class="form-control" />
    </div>

    <div class="form-group">
        <label for="password">Mot de passe <a href="forget.php">(Mot de passe oublié ?)</a></label>
        <input type="password" name="password" id="password" class="form-control" />
    </div>

    <div class="form-group">
        <label for="">
            <input type="checkbox" name="remember" id="remember" value="1" checked />
            Se souvenir de moi
        </label>
    </div>
    <button type="submit" class="btn btn-primary">Se connecter</button>
</form>

<?php require './includes/footer.php'; ?>