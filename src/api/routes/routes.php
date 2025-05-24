<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/PostController.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
$request = str_replace('/api/', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$parts = explode('/', trim($request, '/'));
$body = json_decode(file_get_contents("php://input"));

$db = (new DB())->connect();
$controller = new UserController($db);
$postController = new PostController($db);


switch ($parts[0]) {
    case 'register':
        echo json_encode($controller->register($body));
        break;

    case 'login':
        echo json_encode($controller->login($body));
        break;
    // Rotas de postagem
    case 'posts':
        switch ($method) {
             case 'GET':
                if (isset($parts[1]) && $parts[1] === 'me') {
                    echo json_encode($postController->meusPosts());
                } elseif (isset($parts[1])) {
                    echo json_encode($postController->show($parts[1]));
                } else {
                    echo json_encode($postController->list());
                }
                break;
            case 'POST':
                echo json_encode($postController->create($body));
                break;
            case 'PUT':
            case 'PATCH':
                // PUT /posts/{id}
                if (isset($parts[1])) {
                    echo json_encode($postController->update($parts[1], $body));
                } else {
                    http_response_code(400);
                    echo json_encode(["status" => "error", "message" => "ID da postagem é obrigatório."]);
                }
                break;
            case 'DELETE':
                // DELETE /posts/{id}
                if (isset($parts[1])) {
                    echo json_encode($postController->delete($parts[1]));
                } else {
                    http_response_code(400);
                    echo json_encode(["status" => "error", "message" => "ID da postagem é obrigatório."]);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(["status" => "error", "message" => "Método não permitido."]);
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Rota não encontrada"]);
}
?>