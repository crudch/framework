<?php

/** @noinspection PhpIncludeInspection */
return [
    /**
     * Загрузка Config
     */
    'global_config' => require root_path('/config.php'),

    /**
     * Инициализация Request
     */
    \Crudch\Http\Request::class => static function () {
        return new \Crudch\Http\Request();
    },

    /**
     * Инициализация подключения базе данных
     */
    \Crudch\Database\Connection::class => static function () {
        return \Crudch\Database\Connection::connect();
    },

    /**
     * Инициализация Cache
     */
    \Crudch\Cache\Cache::class => static function () {
        $cache = config('cache_driver');

        return new \Crudch\Cache\Cache(new $cache(root_path('/cache')));
    },
];