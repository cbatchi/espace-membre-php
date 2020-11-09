<?php
require './lib/lib.php';

auto_connect_from_cookie();

if (isset($_SESSION['auth'])) { 
    redirectTo('account.php');
    exit();
}

// Run script if $_POST is not empty
if (!empty($_POST)) {
    $errors = [];

    if (empty($_POST['username'])) $errors['username'] = 'Mauvais pseudo ou email';
    if (empty($_POST['password'])) $errors['password'] = 'Votre mot de passe est incorrect';

    if (empty($errors)) {
        require './includes/db.php';

        $req = $pdo->prepare("SELECT * FROM $table WHERE (username=:username OR email=:username) AND confirmed_at IS NOT NULL");
        $req->bindParam(':username', $_POST['username'], PDO::PARAM_STR);
        $req->execute();
        $user = $req->fetch();

        if ($user && password_verify($_POST['password'], $user->password)) {
            
            $_SESSION['auth'] = $user;
            sessionFlashMessage('success', 'Vous êtes maintenant connécté');

            if ($_POST['remember']) {
                $remember_token = str_random(250);
                $req = $pdo->prepare("UPDATE $table SET remember_token=:remember_token WHERE id=:id");
                $req->bindParam(':id', $user->id, PDO::PARAM_INT);
                $req->bindParam(':remember_token', $remember_token, PDO::PARAM_STR);
                $req->execute();

                setcookie(
                    'remember', $user->id . '//' . $remember_token . sha1($user->id . 'localdev'), 
                    strtotime('+1 day'),
                    '/'
                );
            }
            redirectTo('account.php');
            exit();
        } else {
            sessionFlashMessage('danger', 'Identifiants (username or email / password) incorrects');
        }
    }
}