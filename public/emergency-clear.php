<?php

$secret = $_GET['secret'] ?? '';
$expected = getenv('SYSTEM_SECRET_KEY') ?: 'market-secret-99';

if (!hash_equals($expected, $secret)) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$root = dirname(__DIR__);
$targets = [
    $root . '/bootstrap/cache/*.php',
    $root . '/storage/framework/views/*.php',
];

$deleted = [];

foreach ($targets as $pattern) {
    foreach (glob($pattern) ?: [] as $file) {
        if (is_file($file) && unlink($file)) {
            $deleted[] = str_replace($root . '/', '', $file);
        }
    }
}

header('Content-Type: text/plain; charset=UTF-8');
echo "Emergency cache clear complete.\n";
echo "Deleted files: " . count($deleted) . "\n";
foreach ($deleted as $file) {
    echo "- {$file}\n";
}
