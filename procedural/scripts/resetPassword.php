<?php

session_start();

require './includes/db.php';
require './lib/lib.php';

if (isset($_GET['userId'], $_GET['token'])) {

    $req = $pdo->prepare("SELECT * FROM $table WHERE id=:id 
        AND reset_token IS NOT NULL
        AND reset_token=:reset_token 
        AND reset_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE)
    ");
    $req->bindParam(':id', $_GET['userId'], PDO::PARAM_INT);
    $req->bindParam(':reset_token', $_GET['token'], PDO::PARAM_STR);
    $req->execute();

    $user = $req->fetch();

    if ($user) {
        if (!empty($_POST)) {

            $errors = [];

            if(
                empty($_POST['password']) ||
                !preg_match($patterns[2], $_POST['password']) ||
                $_POST['password'] !== $_POST['password-confirm']
            ) {
                $errors['password'] = 'Ce formulaire est soit vide / les champs ne correspondent pas';
            } else {
                $passwordhash = password_hash($_POST['password'], PASSWORD_BCRYPT);

                $req = $pdo->prepare("UPDATE $table SET password=:password, reset_at=NULL, reset_token=NULL WHERE id=:id");
                $req->bindParam(':password', $passwordhash, PDO::PARAM_STR);
                $req->bindParam(':id', $user->id, PDO::PARAM_INT);
                $req->execute();

                sessionFlashMessage('success', 'Votre mot de passe a été renitialiser, merci de vous connecter');
                redirectTo('signin.php');

                exit();
            }
        }
    } else {
        sessionFlashMessage('danger', 'Ce token n\'est plus valide');
        redirectTo('signin.php');
        exit();
    }

} else {
    redirectTo('signin.php');
    exit();
}