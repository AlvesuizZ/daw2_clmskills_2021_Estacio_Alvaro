<?php
function cargarEnv($ruta) {
    if (!file_exists($ruta)) {
        throw new Exception(".env file not found");
    }

    $variables = parse_ini_file($ruta);
    foreach ($variables as $key => $value) {
        putenv("$key=$value");
    }
}

cargarEnv(__DIR__ . '/../.env');

$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
