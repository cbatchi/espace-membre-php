<?php

session_start();

setcookie('remember', NULL, strtotime('-1 day'));

require './lib/lib.php';

unset($_SESSION['auth']);

sessionFlashMessage('success', 'Vous êtes correctement deconnecter');

redirectTo('signin.php');