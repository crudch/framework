<?php

namespace Crudch\Database;

use PDO;

class Connection extends PDO
{
    public function __construct(string $dsn, array $config)
    {
        parent::__construct($dsn, $config['username'], $config['password'], $config['options']);
    }

    public static function connect()
    {
        $config = config('db');

        return static::{$config['driver']}($config[$config['driver']]);
    }

    /**
     * @param array $config
     *
     * @return Connection
     */
    protected static function mysql(array $config): Connection
    {
        $dsn = "mysql:dbname={$config['database']};host={$config['host']};charset={$config['charset']}";

        return new static($dsn, $config);
    }

    /**
     * @param array $config
     *
     * @return Connection
     */
    protected static function sqlite(array $config): Connection
    {
        $dsn = "sqlite:{$config['database']}";

        foreach (['username', 'password', 'options'] as $item) {
            $config[$item] = null;
        }

        return new static($dsn, $config);
    }
}
