<?php


use console\WebSocketServer;

$server = new WebSocketServer(config('socket'));
$server->run();
