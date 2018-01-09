<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2018-01-09
 */

namespace fk\utility\Console;

use Illuminate\Console\Command;

class InitDatabaseCommand extends Command
{
    public $name = 'init:db';

    public $description = 'Create the database if not exists. Currently support only for MySQL';

    public function handle()
    {
        $config = config('database.connections.mysql');
        $pdo = new \PDO("mysql:host=${config['host']}", $config['username'], $config['password']);
        $pdo->exec(<<<SQL
CREATE DATABASE IF NOT EXISTS {$config['database']} CHARSET {$config['charset']} COLLATE {$config['collation']}
SQL
        );
        $stmt = $pdo->query("SHOW DATABASES LIKE '%{$config['database']}%'");
        $stmt->rowCount()
            ? $this->info('Database initialized.')
            : $this->error('Database initializing failed');
    }
}