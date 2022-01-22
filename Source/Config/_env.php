<?php
use Dotenv\Dotenv;

$env = Dotenv::createImmutable(BASE_PATH);
$env->load();
$env->required(['DB_DSN', 'DB_USER', 'DB_PASS', 'DB_NAME', 'DB_HOST', 'DB_PORT']);