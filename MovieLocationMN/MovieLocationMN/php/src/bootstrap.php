<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function view(string $tpl, array $data = []): void {
    extract($data);
    $tplPath = __DIR__ . '/../templates/' . $tpl . '.php';
    if (!is_file($tplPath)) {
        http_response_code(500);
        echo "Template missing: " . htmlspecialchars($tplPath);
        exit;
    }
    require $tplPath;
}
