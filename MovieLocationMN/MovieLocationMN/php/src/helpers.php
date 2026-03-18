<?php
declare(strict_types=1);

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

function flash(string $msg, string $type = 'ok'): void {
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}

function consume_flashes(): array {
    $msgs = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $msgs;
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
