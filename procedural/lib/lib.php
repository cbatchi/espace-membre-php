<?php

$patterns = [
    '/^[a-zA-Z0-9_]+$/',
    '/^[a-z0-9][-a-z0-9._]+@([-a-z0-9]+[.])+[a-z]{2,5}$/',
    '/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/'
];


function str_random (int $length): string {
    $alphabet = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN0123456789';
    return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
}

function redirectTo (string $name): void {
    header('Location: ' . $name);
}

function setSession (string $key, $data) {
    $_SESSION[$key] = $data;
}

function sessionFlashMessage (string $key, string $message) {
    $_SESSION['flash'][$key] = $message;
}

function SessionKey (string $key) {
    $_SESSION[$key];
}

function sessionKeyExist (string $key) {
    return isset($_SESSION[$key]);
}

function onlyLogged () {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['auth'])) {
        sessionFlashMessage('danger', 'Accès refuser, vous devez vous connecter avant');
        redirectTo('signin.php');
        exit();
    }
}

function displayErrors () {

    if(!empty($errors)) {
        $err = '';
        foreach($errors as $error) {
            $err = $error;
        }
        return '<div class="alert alert-danger">
                <p>Ce formulaire contient des erreurs</p>
                <ul><li>' . $err .'</li></ul>
            </div>';
    }
}

function auto_connect_from_cookie () {
    require './includes/db.php';
    // Start session if not
    if (session_status() == PHP_SESSION_NONE) session_start();
    // Check if remember key exist inside $_COOKIE array
    if (isset($_COOKIE['remember']) && !isset($_SESSION['auth'])) {
        // get token store inside cookie
        $remember_token = $_COOKIE['remember'];
        // Get the first item of after explode
        $userId = explode('//', $remember_token)[0];

        $req = $pdo->prepare("SELECT * FROM $table WHERE id=:id");
        $req->bindParam(':id', $userId, PDO::PARAM_INT);
        $req->execute();
        $user = $req->fetch();

        if ($user) {
            $expected = $userId . '//' . $user->remember_token . sha1($userId . 'localdev');
            if ($expected == $remember_token) {
                $_SESSION['auth'] = $user;
                setcookie('remember', $remember_token, strtotime('+1 day'));
            } else {
                setcookie('remember', NULL, strtotime('-1 day'));
            }
        } else {
            setcookie('remember', NULL, strtotime('-1 day'));
        }
    }
}

function start_session () {
    if (session_status() == PHP_SESSION_NONE) return session_start();
}