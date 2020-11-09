<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management System</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="../assets/css/app.css">
</head>

<body>
  <header class="header">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <a class="navbar-brand" href="index.php">Navbar</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <?php if ($app->sessions->getSessionKey('auth')) : ?>
            <li class="nav-item active">
              <a class="nav-link" href="logout.php">Se deconnecter</a>
            </li>
          <?php else : ?>
            <li class="nav-item active">
              <a class="nav-link" href="register.php">S'inscrire</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="login.php">Se connecter</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </nav>
  </header>

  <section class="section">
    <section class="main">
      <div class="container">
        <div class="box-form">
          <?php if ($app->sessions->getSessionKey('flash')) : ?>
            <?php foreach ($app->sessions->getSessionKey('flash') as $type => $message) : ?>
              <div class="alert alert-<?= $type; ?>"><?= $message; ?></div>
            <?php endforeach; ?>
            <?php $app->sessions->unsetSessions('flash'); ?>
          <?php endif; ?>