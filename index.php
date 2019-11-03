<?php
$baseUri = '/tests/test_20';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $request = $baseUri . '/' . $_GET['param'];
} 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request = $_SERVER['REQUEST_URI'];
}

switch ($request) {
    case $baseUri . '/' :
        require __DIR__ . '/app.html';
        break;
    case $baseUri . '/GrowApples' :
        require __DIR__ . '/c/GrowApples.php';
        break;
    case $baseUri . '/EditApple' :
        require __DIR__ . '/c/EditApple.php';
        break;
    default:
        http_response_code(404);
        require __DIR__ . '/c/Default.php';
        break;
}