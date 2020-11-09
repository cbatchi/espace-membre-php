<?php
require './includes/bootstrap.php';

$app = App::getInstance();

// Détruit les informations contenues dans la session au niveau de la clé auth
$app->sessions->unsetSessions('auth');

// Affiche un message sur le deroulement de l'opération
$app->sessions->setSessionFlashMessages('success', 'Vous êtes correctement deconnecter');

// Redirige vers la page login
$app->sessions->redirectTo('login');