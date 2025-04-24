<?php
global $router;
$router->post(path: '/like', action: 'LikeController@likeUser');
$router->get(path: '/user-matches', action: 'LikeController@listMatches');


// src/Routes/web.php
$router->get(path: '/set-session', action: function() {
    session_start();
    $_SESSION['user_id'] = 1;
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'user_id' => 1]);
});
