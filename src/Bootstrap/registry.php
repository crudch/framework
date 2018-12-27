<?php

return [
    /**
     * Загружает конфиг
     */
    \Crudch\Config\Config::class => function () {
        return new \Crudch\Config\Config(root_path() . '/config.php');
    },

    /**
     * Инициализирует Request
     */
    \Crudch\Http\Request::class => function () {
        return new \Crudch\Http\Request();
    },

    /**
     * Инифиализирует подключение базе данных
     */
    \Crudch\Database\Connection::class => function () {
        return new \Crudch\Database\Connection(config('db'));
    },

    /**
     * Инициализирует кэш
     */
    \Crudch\Cache\Cache::class => function () {
        $cache = config('cache_driver');

        return new \Crudch\Cache\Cache(new $cache(root_path() . '/cache'));
    },
];