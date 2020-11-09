<?php

session_start();

$userId = $_GET['userId'];
$token = $_GET['token'];

require './includes/db.php';
require './lib/lib.php';

try {
    $req = $pdo->prepare("SELECT * FROM $table WHERE id=:id");
    $req->bindParam(':id', $userId, PDO::PARAM_INT);
    $req->execute();
    $user = $req->fetch();

    if ($user && $user->confirmation_token == $token) {
        $req = $pdo->prepare("UPDATE $table SET confirmation_token=NULL, confirmed_at=NOW() WHERE id=:id");
        $req->bindParam(':id', $userId, PDO::PARAM_INT);
        $req->execute();
        sessionFlashMessage('success', 'Votre compte a bien été confirmé');
        setSession('auth', $user);
        redirectTo('account.php');
        
    } else {
        sessionFlashMessage('danger', 'Ce token n\' est pas ou plus valide');
        redirectTo('signin.php');
    }
} catch (Exception $e) {
    die('Error ' . $e->getMessage());
}