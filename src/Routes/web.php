<?php
global $router;

// Rotas de curtidas e matches
$router->post(path: '/like', action: 'LikeController@likeUser');
$router->get(path: '/user-matches', action: 'LikeController@listMatches');
$router->get(path: '/to-like-list', action: 'LikeController@likeList');

// Rotas de autenticação e usuário
$router->post(path: '/register', action: 'UserController@register');
$router->post(path: '/login', action: 'UserController@login');
$router->post(path: '/recover-password', action: 'UserController@recoverPassword');
$router->post(path: '/reset-password', action: 'UserController@resetPassword');

// Rota temporária para simular login com sessão (para testes)
$router->get(path: '/set-session', action: function () {
    session_start();
    $_SESSION['user_id'] = 1;
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'user_id' => 1]);
});

$router->get(path: '/matches', action: function () {
    require_once __DIR__ . '/matches.php';
});

