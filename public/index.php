<?php
/**
 * Public Entry Point
 */

$router = require_once dirname(__DIR__) . '/app/bootstrap.php';
$router->dispatch();
