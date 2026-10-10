<?php

// Only used by start-local.ps1 with PHP's development server.
if (PHP_SAPI !== 'cli-server') {
    exit('This router is for the PHP development server.');
}
$base = getenv('POS_LOCAL_BASE_URL');
if (is_string($base) && preg_match('~^http://localhost:[0-9]+/$~D', $base)) {
    $_ENV['app.baseURL'] = $base;
    $_SERVER['app.baseURL'] = $base;
}
return require dirname(__DIR__) . '/vendor/codeigniter4/framework/system/rewrite.php';
