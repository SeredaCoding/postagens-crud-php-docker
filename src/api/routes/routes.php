<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/UserController.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
$request = str_replace('/api/', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$parts = explode('/', trim($request, '/'));
$body = json_decode(file_get_contents("php://input"));

$db = (new DB())->connect();
$controller = new UserController($db);

switch ($parts[0]) {
    case 'register':
        echo json_encode($controller->register($body));
        break;

    case 'login':
        echo json_encode($controller->login($body));
        break;

    case 'users':
        if ($method === 'GET' && count($parts) === 1) {
            echo json_encode($controller->list());
        } elseif ($method === 'GET' && count($parts) === 2) {
            echo json_encode($controller->show($parts[1]));
        } elseif ($method === 'PUT' && count($parts) === 2) {
            echo json_encode($controller->update($parts[1], $body));
        } elseif ($method === 'DELETE' && count($parts) === 2) {
            echo json_encode($controller->delete($parts[1]));
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Requisição inválida."]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Rota não encontrada"]);
}
?>