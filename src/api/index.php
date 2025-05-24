<?php
header('Content-Type: application/json');
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = str_replace('/api/', '', $request);

$user = require __DIR__ . '/controllers/UserController.php';
$data = json_decode(file_get_contents("php://input"));

switch ($request) {
    case 'register':
        $user->nome = $data->nome ?? null;
        $user->email = $data->email ?? null;
        $user->senha = $data->senha ?? null;

        echo $user->register();
        break;

    case 'login':
        $user->email = $data->email ?? null;
        $user->senha = $data->senha ?? null;

        echo $user->login();
        break;

    case '':
        echo json_encode([
            "status" => "success",
            "message" => "API funcionando",
            "data" => null,
            "errors" => null
        ]);
        break;

    default:
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Rota não encontrada",
            "data" => null,
            "errors" => null
        ]);
        break;
}
