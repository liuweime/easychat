<?php


require_once './CustomSessionHandle.php';

$handle = new CustomSessionHandle();
session_set_save_handler($handler, true);
session_start();
