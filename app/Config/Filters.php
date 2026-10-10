<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;

class Filters extends BaseFilters
{
    public array $aliases = [
        'csrf' => \CodeIgniter\Filters\CSRF::class,
        'forcehttps' => \CodeIgniter\Filters\ForceHTTPS::class,
        'invalidchars' => \CodeIgniter\Filters\InvalidChars::class,
        'secureheaders' => \CodeIgniter\Filters\SecureHeaders::class,
        'auth' => \App\Filters\AuthFilter::class,
    ];
    public array $required = ['before' => ['forcehttps'], 'after' => []];
    public array $globals = [
        'before' => ['invalidchars', 'csrf'],
        'after' => ['secureheaders'],
    ];
    public array $methods = [];
    public array $filters = [];
}

