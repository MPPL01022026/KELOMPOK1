<?php
/**
 * Single Entry Point - Yunda Fashion POS & Inventory System
 */

$router = require_once __DIR__ . '/app/bootstrap.php';
$router->dispatch();
