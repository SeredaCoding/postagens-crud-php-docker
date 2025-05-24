<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

$database = new DB();
$db = $database->connect();
$user = new User($db);

// Retorne o objeto para quem incluir este arquivo
return $user;
?>