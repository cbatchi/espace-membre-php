<?php

session_start();

if (!empty($_POST)) {

    require './includes/db.php';
    require './lib/lib.php';

    $errors = [];

    if (empty($_POST['username']) || !preg_match($patterns[0], $_POST['username'])) {
        $errors['username'] = 'Votre pseudo n\'est pas valide (Alphanumérique)';
    } else {
        $req = $pdo->prepare("SELECT id FROM $table WHERE username=:username");
        $req->bindParam(':username', $_POST['username'], PDO::PARAM_STR);
        $req->execute();
        $user = $req->fetch();
        
        if ($user) $errors['username'] = 'Ce pseudo est deja pris';
    }

    if (empty($_POST['email']) || !preg_match($patterns[1], $_POST['email'])) {
        $errors['email'] = 'Le format de cet email n\'est pas valide';
    } else {
        $req = $pdo->prepare("SELECT id FROM $table WHERE email=:email");
        $req->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $req->execute();
        $user = $req->fetch();
        
        if ($user) $errors['email'] = 'Cet email est déjà associé à un compte';
    }

    if (empty($_POST['password']) || !preg_match($patterns[2], $_POST['password']) || $_POST['password'] !== $_POST['password-confirm']) {
        $errors['password'] = 'Votre mot de passe est incorrect ou ne correspondent pas';
    }

    if (empty($errors)) {

        $req = $pdo->prepare('INSERT INTO users (username, email, password, confirmation_token) VALUES (:username, :email, :password, :confirmation_token)');
        $token = str_random(60);
        
        $passwordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $req->bindParam(':username', $_POST['username'], PDO::PARAM_STR);
        $req->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $req->bindParam(':password', $passwordHash, PDO::PARAM_STR);
        $req->bindParam(':confirmation_token', $token, PDO::PARAM_STR);
        $req->execute();

        $userId = $pdo->lastInsertId();

        $uri = "http://localhost:8000/confirm.php?userId=$userId&token=$token";
        $buttons = '<a href=' . $uri . '>Confirmer</a>';
        $subject = 'Confirmation de votre compte';
        $message= 'Afin de confirmer votre compte, merci de cliquer sur ce lien ' . $buttons;

        mail($_POST['email'], $subject, $message);

        $_SESSION['flash']['success'] = 'Un mail de confirmation vous a été envoyé';

        header('Location: signin.php');
        exit();
    }
}