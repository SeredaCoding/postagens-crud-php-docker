<?php
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = str_replace('/api/', '', $request);
switch ($request) {
    case 'register':
        die('register');
        require __DIR__ . '/controllers/UserController.php';
        break;
    case 'login':
        die('login');
        require __DIR__ . '/controllers/UserController.php';
        break;
    default:
        require __DIR__ . '/controllers/UserController.php';
        http_response_code(404);
        echo json_encode(['message' => 'Rota não encontrada']);
        break;
}
