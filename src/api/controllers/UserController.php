<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

$database = new DB();
$db = $database->connect();

$user = new User($db);

echo ('aqui');


// POST para criar usuário
// $data = json_decode(file_get_contents("php://input"));
// $user->nome = $data->nome;
// $user->email = $data->email;
// $user->senha = $data->senha;

// if ($user->register()) {
//     echo json_encode(["message" => "Usuário criado com sucesso."]);
// } else {
//     echo json_encode(["message" => "Erro ao criar usuário."]);
// }
