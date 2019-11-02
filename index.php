<?php
require_once "route.php";
$baseUri = '/tests/test_20';

route($baseUri . '/GrowApples', function () {
    require __DIR__ . '/c/GrowApples.php';
});

route($baseUri . '/EditApple', function () {
    require __DIR__ . '/c/EditApple.php';
});

route($baseUri . '/', function () {
    require __DIR__ . '/app.html';
});

$action = $_SERVER['REQUEST_URI'];
dispatch($action);