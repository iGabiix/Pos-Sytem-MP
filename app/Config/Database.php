<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;
    public string $defaultGroup = 'default';

    public array $default = [
        'DSN' => '', 'hostname' => '127.0.0.1', 'username' => '', 'password' => '',
        'database' => 'feu_pos', 'DBDriver' => 'MySQLi', 'DBPrefix' => '',
        'pConnect' => false, 'DBDebug' => ENVIRONMENT !== 'production',
        'charset' => 'utf8mb4', 'DBCollat' => 'utf8mb4_unicode_ci',
        'swapPre' => '', 'encrypt' => false, 'compress' => false,
        'strictOn' => true, 'failover' => [], 'port' => 3306,
        'foreignKeys' => true, 'busyTimeout' => 5000,
        'dateFormat' => ['date' => 'Y-m-d', 'datetime' => 'Y-m-d H:i:s', 'time' => 'H:i:s'],
    ];

    public array $tests = [
        'DSN' => '', 'hostname' => '127.0.0.1', 'username' => '', 'password' => '', 'port' => 3306,
        'database' => ':memory:', 'DBDriver' => 'SQLite3', 'DBPrefix' => '',
        'pConnect' => false, 'DBDebug' => true, 'charset' => 'utf8', 'DBCollat' => '',
        'swapPre' => '', 'strictOn' => true, 'failover' => [],
        'foreignKeys' => true, 'busyTimeout' => 5000,
        'dateFormat' => ['date' => 'Y-m-d', 'datetime' => 'Y-m-d H:i:s', 'time' => 'H:i:s'],
    ];

    public function __construct()
    {
        parent::__construct();
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        } elseif ($this->default['DBDriver'] === 'SQLite3' && $this->default['database'] === 'feu_pos') {
            $this->default['database'] = WRITEPATH . 'pos.sqlite';
        }
    }
}
