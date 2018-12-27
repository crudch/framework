<?php

namespace Crudch\Database;

class Connection extends \PDO
{
    public function __construct(array $config)
    {
        parent::__construct(
            "mysql:dbname={$config['dbname']};host={$config['host']};charset=utf8mb4",
            $config['username'],
            $config['password'],
            $config['options']
        );
    }
}
