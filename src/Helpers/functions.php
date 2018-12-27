<?php

/**
 * @param string $name
 *
 * @return mixed|object
 */
function app($name)
{
    return \Crudch\Container\Container::get($name);
}

function config($key)
{
    return app('config')[$key];
}

/**
 * @return \PDO
 */
function db()
{
    return app(\Crudch\Database\Connection::class);
}

/**
 * @return \Crudch\Http\Request
 */
function request()
{
    return app(\Crudch\Http\Request::class);
}

/**
 * @return \Crudch\Cache\Cache
 */
function cache()
{
    return app('cache');
}

/**
 * @param string $url
 * @param int    $code
 *
 * @return \Crudch\Http\Response
 */
function redirect($url, $code = 302)
{
    return (new \Crudch\Http\Response())
        ->redirect($url, $code);
}

/**
 * @return \Crudch\Http\Response
 */
function back()
{
    return (new \Crudch\Http\Response())
        ->back();
}

/**
 * @param mixed $data
 * @param int   $code
 *
 * @return \Crudch\Http\Response
 */
function json($data, $code = 200)
{
    return (new \Crudch\Http\Response())
        ->json($data, $code);
}

/**
 * @param int  $code
 * @param null $data
 */
function abort(int $code = 404, $data = null)
{
    (new \System\Http\Response())->abort($code, $data);
}

/** @noinspection PhpDocMissingThrowsInspection */
/**
 * @param string $name
 * @param array  $params
 *
 * @return string
 */
function render($name, array $params = [])
{
    static $path;

    /** @noinspection PhpUnhandledExceptionInspection */
    return (new \System\View($path ?: $path = config('view')['path']))
        ->render($name, $params);
}

/**
 * @param string $name
 * @param array  $params
 *
 * @return \System\Http\Response
 */
function view($name, array $params = [])
{
    return (new \System\Http\Response())
        ->setData(render($name, $params));
}

function old($name, $default = null)
{
    if (empty($_SESSION[$name])) {
        return $default;
    }

    $data = $_SESSION[$name];
    unset($_SESSION[$name]);

    return $data;
}


/**
 * Экранирует теги
 *
 * @param $string string
 *
 * @return string string
 */
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE);
}


/**
 * @param string $time
 *
 * @return \System\WarDate
 */
function wdate($time = 'now')
{
    try {
        $date = new \System\WarDate($time);
    } catch (\Throwable $e) {
        $date = null;
    }

    return $date;
}

/**
 * @param string   $key
 * @param callable $callback
 * @param int      $time
 *
 * @return mixed
 */
function remember($key, callable $callback, $time = 0)
{
    if (!$cache = cache()->get($key)) {
        $cache = $callback();
        cache()->set($key, $cache, $time);
    }

    return $cache;
}


/**
 * @param callable $callback
 *
 * @return bool
 * @throws \System\Exceptions\TransactionException
 */
function transaction(callable $callback)
{
    $db = db();

    try {
        $db->beginTransaction();
        $callback();

        return $db->commit();
    } catch (\Throwable $e) {
        $db->rollBack();

        throw new \System\Exceptions\TransactionException('Неудачная транзакция', 500, $e);
    }
}


/**
 * @todo Доделать
 *
 * @param $url
 *
 * @return string
 */
function url($url)
{
    return $url;
}

/**
 * Получить абсолютную ссылку
 *
 * @param $url
 *
 * @return string
 */
function absUrl($url)
{
    static $full_url;

    return ($full_url ?: $full_url = config('url')) . '/' . trim($url, '/');
}

/**
 * Проверка на прокашн
 *
 * @return bool
 */
function isProduction()
{
    static $env;

    return $env ?? $env = 'production' === config('env');
}

/**
 * Окружение
 *
 * @return bool
 */
function isLocal()
{
    return !isProduction();
}

/**
 * @param $string [FooBarBaz]
 *
 * @return string [fooBarBaz]
 */
function camel($string)
{
    return lcfirst(studly($string));
}

/**
 * @param $string [foo_bar_baz]
 *
 * @return string [FooBarBaz]
 */
function studly($string)
{
    return implode('', array_map('ucfirst', explode('_', $string)));
}

/**
 * @param mixed    $value
 * @param callable $callback
 *
 * @return mixed
 */
function tap($value, callable $callback)
{
    $callback($value);

    return $value;
}

/**
 * @param string $text
 * @param int    $sub
 * @param string $end
 *
 * @return string
 */
function subText($text, int $sub, $end = '')
{
    if (mb_strlen($text) > $sub) {
        $text = mb_substr($text, 0, $sub);
        $text = mb_substr($text, 0, mb_strrpos($text, ' '));
        $text .= $end;
    }

    return $text;
}

function convertBite($bytes)
{
    $prefix = ['B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB'];

    $class = min((int)log($bytes, 1024), count($prefix) - 1);

    return sprintf('%1.2f %s', $bytes / 1024 ** $class, $prefix[$class]);
}

/**
 * @param array $array
 *
 * @return array
 */
function flatten($array)
{
    $return = [];

    array_walk_recursive($array, function ($a) use (&$return) {
        $return[] = $a;
    });

    return $return;
}

/**
 * Генерирует уникальный токен для пользователя размером в 64 символа на основании соли, например email
 *
 * @param string $salt
 *
 * @return string
 */
function token($salt)
{
    return hash_hmac('gost', $salt . randomString(32), time());
}

/**
 * Получить рандомную строку
 *
 * @param int $length
 *
 * @return string
 */
function randomString($length = 32)
{
    try {
        $string = bin2hex(random_bytes((int)(($length - ($length % 2)) / 2)));
    } catch (Throwable $e) {
        $alpha = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        while (strlen($alpha) < $length) {
            $alpha .= $alpha;
        }

        $string = str_shuffle(substr(str_shuffle($alpha), 0, $length));
    }

    return $string;
}

/**
 * @param $string
 *
 * @return string
 */
function compress($string)
{
    $replace = [
        '#>[^\S ]+#s'                                                     => '>',
        '#[^\S ]+<#s'                                                     => '<',
        '#([\t ])+#s'                                                     => ' ',
        '#^([\t ])+#m'                                                    => '',
        '#([\t ])+$#m'                                                    => '',
        '#//[a-zA-Z0-9 ]+$#m'                                             => '',
        '#[\r\n]+([\t ]?[\r\n]+)+#s'                                      => "\n",
        '#>[\r\n\t ]+<#s'                                                 => '><',
        '#}[\r\n\t ]+#s'                                                  => '}',
        '#}[\r\n\t ]+,[\r\n\t ]+#s'                                       => '},',
        '#\)[\r\n\t ]?{[\r\n\t ]+#s'                                      => '){',
        '#,[\r\n\t ]?{[\r\n\t ]+#s'                                       => ',{',
        '#\),[\r\n\t ]+#s'                                                => '),',
        '#([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\\-]+)"([\r\n\t ])?#s' => '$1$2=$3$4',
    ];

    return preg_replace(array_keys($replace), array_values($replace), $string);
}

/**
 * @param int    $number
 * @param string $words [1|2|0] - [год|года|лет]
 *
 * @return string
 */
function plural($number, $words)
{
    $tmp = explode('|', $words);

    if (count($tmp) < 3) {
        return '';
    }

    /** @noinspection NestedTernaryOperatorInspection */
    return $tmp[(($number % 10 === 1) && ($number % 100 !== 11)) ? 0 :
        ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2)];
}
