<?php
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
echo ('<pre>');
echo ($request);
echo ('</pre>');
$request = str_replace('/api/', '', $request);
switch ($request) {
    case 'register':
        die('Rota identificada corretamente!');
        require __DIR__ . '/controllers/UserController.php';
        break;
    case 'login':
        require __DIR__ . '/controllers/UserController.php';
        break;
    default:
        require __DIR__ . '/controllers/UserController.php';
        // http_response_code(404);
        // echo json_encode(['message' => 'Rota não encontrada']);
        break;
}
