<?php

if (!empty($_POST)) {
    require './includes/db.php';

    $errors = [];

    if (
        empty($_POST['password']) || 
        !preg_match($patterns[2], $_POST['password']) || 
        $_POST['password'] !== $_POST['password-confirm']) 
    {
        $errors['password'] = 'Vos mot de passes ne correspondent pas ou ne sont pas conformes';
    }

    if (empty($errors)) {
        $userId = $_SESSION['auth']->id;
        $passwordhash = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $req = $pdo->prepare("UPDATE $table SET password=:password WHERE id=:id");
        $req->bindParam(':id', $userId, PDO::PARAM_INT);
        $req->bindParam(':password', $passwordhash, PDO::PARAM_STR);
        $req->execute();

        sessionFlashMessage('success', 'Votre mot de passe a été mis à jour');
        redirectTo('signin.php');

        exit();
    }
}