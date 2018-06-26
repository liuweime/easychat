<?php


require_once './Server.php';
$config = require_once '../config/config.php';

$server = new WebSocketServer($config['socket']);
$server->run();
