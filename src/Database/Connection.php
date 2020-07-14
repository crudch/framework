<?php

namespace Crudch\Database;

use PDO;

class Connection extends PDO
{
    public function __construct(string $dsn, array $config)
    {
        parent::__construct(
            "mysql:dbname={$config['dbname']};host={$config['host']};charset=utf8mb4",
            $config['username'],
            $config['password'],
            $config['options']
        );
    }

    public static function connect()
    {

    }
}
