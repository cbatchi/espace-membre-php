<?php

session_start();

if (!empty($_POST)) {
    require './lib/lib.php';
    require './includes/db.php';

    $errors = [];

    if (
        empty($_POST['email']) ||
        !preg_match($patterns[1], $_POST['email']) || $_POST['email'] !== $_POST['email-confirm']
    ) {
        $errors['email'] = 'Le format de cet email n\'est pas valide ou ne correspondent pas';
    } else {

        $req = $pdo->prepare("SELECT * FROM $table WHERE email=:email AND confirmed_at IS NOT NULL");
        $req->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $req->execute();
        $user = $req->fetch();

        if ($user) {
            $reset_token = str_random(60);
            
            $req = $pdo->prepare("UPDATE $table SET reset_token=:reset_token, reset_at=NOW() WHERE id=:id");
            $req->bindParam(':reset_token', $reset_token, PDO::PARAM_STR);
            $req->bindParam(':id', $user->id, PDO::PARAM_STR);
            $req->execute();

            sessionFlashMessage('success', 'Les instructions de recuperation de mots de passe vous ont été envoyé');

            $uri = "http://localhost:8000/reset.php?userId=$user->id&token=$reset_token";
            $buttons = '<a href=' . $uri . ' target=_blank>Confirmer</a>';
            $subject = 'Rénitialisation de votre mot de passe';
            $message= 'Afin de renitialiser votre mot de passe, merci de cliquer sur ce lien ' . $buttons;

            mail($_POST['email'], $subject, $message);

            redirectTo('signin.php');
            exit();
        } else {
            sessionFlashMessage('danger', 'Aucun  compte n\' est associé à cet email');
        }
    }
}