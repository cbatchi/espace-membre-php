<?php
$table = 'users';
try {
    $pdo = new PDO("mysql:host=localhost;dbname=user_management", '/* Your username*/', '/*Your password*/', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
} catch (PDOException $e) {
    die('Connection failed '. $e->getMessage());
}