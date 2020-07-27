<?php
/** @noinspection PhpDocMissingThrowsInspection */

/** @noinspection PhpUnhandledExceptionInspection */

use Crudch\App;
use Crudch\View\View;
use Crudch\Cache\Cache;
use Crudch\Http\Request;
use Crudch\Http\Response;
use Crudch\Date\CrutchDate;
use Crudch\Database\Connection;
use Crudch\Http\Exceptions\AbortException;
use Crudch\Database\Exceptions\TransactionException;

/** @noinspection PhpUnhandledExceptionInspection */

/**
 * @param string $name
 *
 * @return mixed|object
 */
function app(string $name)
{
    return App::get($name);
}

/**
 * @param string $key
 *
 * @return mixed
 */
function config(?string $key = null)
{
    $config = app('global_config');

    return null === $key ? $config : $config[$key] ?? null;
}

/**
 * @param string|null $path
 *
 * @return string
 */
function root_path(?string $path = null): string
{
    static $root;

    return ($root ?: $root = app('root_path')) . $path;
}

/**
 * @param string|null $path
 *
 * @return string
 */
function public_path(?string $path = null): string
{
    return root_path('/public') . $path;
}

/**
 * @return PDO
 */
function db()
{
    return app(Connection::class);
}

/**
 * @return Request
 */
function request()
{
    return app(Request::class);
}

/**
 * @return Cache
 */
function cache()
{
    return app(Cache::class);
}

/**
 * @param string $url
 * @param int $code
 *
 * @return Response
 */
function redirect(string $url, int $code = 302)
{
    return (new Response())
        ->redirect($url, $code);
}

/**
 * @return Response
 */
function back()
{
    return (new Response())
        ->back();
}

/**
 * @param mixed $data
 * @param int $code
 *
 * @return Response
 */
function json($data, int $code = 200)
{
    return (new Response())
        ->json($data, $code);
}

/**
 * @param int $code
 * @param string|null $message
 *
 * @throws AbortException
 */
function abort(int $code = 404, ?string $message = null)
{
    throw new AbortException($message, $code);
}

/**
 * @param string $name
 * @param array $params
 *
 * @return string
 */
function render($name, array $params = [])
{
    return (new View(root_path('/templates')))
        ->render($name, $params);
}

/**
 * @param string $name
 * @param array $params
 *
 * @return Response
 */
function view($name, array $params = [])
{
    return (new Response())
        ->setData(render($name, $params));
}

/**
 * @param string $name
 * @param null $default
 *
 * @return mixed
 */
function old(string $name, $default = null)
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
 * @param string $string
 *
 * @return string
 */
function e($string): string
{
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE);
}


/**
 * @param string $time
 *
 * @return CrutchDate
 */
function crutchDate(string $time = 'now')
{
    return new CrutchDate($time);
}

/**
 * @param string $key
 * @param callable $callback
 * @param int $time
 *
 * @return mixed
 */
function remember(string $key, callable $callback, int $time = 0)
{
    if (false === $cache = cache()->get($key)) {
        $cache = $callback();
        cache()->set($key, $cache, $time);
    }

    return $cache;
}


/**
 * @param callable $callback
 *
 * @return bool
 */
function transaction(callable $callback): bool
{
    $db = db();

    try {
        $db->beginTransaction();
        $callback($db);

        return $db->commit();
    } catch (Throwable $e) {
        $db->rollBack();
        throw new TransactionException('Неудачная транзакция', 500, $e);
    }
}


/**
 * @param $url
 *
 * @return string
 * @todo Доделать
 *
 */
function url($url): string
{
    return $url;
}

/**
 * Получить абсолютную ссылку
 *
 * @param string $url
 *
 * @return string
 */
function absUrl(string $url): string
{
    static $full_url;

    return ($full_url ?: $full_url = config('url')) . '/' . trim($url, '/');
}

/**
 * Проверка окружения на Local
 *
 * @return bool
 */
function isLocal(): bool
{
    static $env;

    return $env ?? $env = 'local' === config('env');
}


/**
 * Проверка окружения на Production
 *
 * @return bool
 */
function isProduction(): bool
{
    return !isLocal();
}

/**
 * @param $string [FooBarBaz]
 *
 * @return string [fooBarBaz]
 */
function camel($string): string
{
    return lcfirst(studly($string));
}

/**
 * @param $string [foo_bar_baz]
 *
 * @return string [FooBarBaz]
 */
function studly($string): string
{
    return implode('', array_map('ucfirst', explode('_', $string)));
}

/**
 * @param mixed $value
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
 * Limit the number of characters in a string.
 *
 * @param string $value
 * @param int $limit
 * @param string $end
 *
 * @return string
 */
function limit(string $value, int $limit = 100, string $end = ' ...'): string
{
    if (mb_strwidth($value, 'UTF-8') <= $limit) {
        return $value;
    }

    return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
}

/**
 * @param int $bytes
 *
 * @return string
 */
function convertBite(int $bytes)
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
function flatten(array $array): array
{
    $return = [];

    array_walk_recursive($array, static function ($a) use (&$return) {
        $return[] = $a;
    });

    return $return;
}

/**
 * Генерирует уникальный token размером в 64 символа на основании соли [не обязательно]
 *
 * @param string|null $salt
 *
 * @return string
 */
function token(?string $salt = null): string
{
    return hash_hmac('gost', $salt . randomString(), time());
}

/**
 * Получить random строку
 *
 * @param int $length
 *
 * @return string
 */
function randomString(int $length = 32): string
{
    try {
        $string = bin2hex(random_bytes((int)(($length - ($length % 2)) / 2)));
    } catch (Throwable $e) {
        $alpha = str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

        while (strlen($alpha) < $length) {
            $alpha .= $alpha;
        }

        $string = str_shuffle(substr(str_shuffle($alpha), 0, $length));
    }

    return $string;
}

/**
 * @param string $string
 *
 * @return string
 */
function compress(string $string): string
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
 * @param int $number
 * @param string $words [1|2|0] - [год|года|лет]
 *
 * @return string
 */
function plural(int $number, string $words): string
{
    $tmp = explode('|', $words);

    if (count($tmp) < 3) {
        return '';
    }

    /** @noinspection NestedTernaryOperatorInspection */
    return $tmp[(($number % 10 === 1) && ($number % 100 !== 11)) ? 0 :
        ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2)];
}

/**
 * Рекурсивно удалить директорию
 *
 * @param string $dir
 *
 * @return bool
 */
function recursiveRemoveDir(string $dir): bool
{
    $includes = new FilesystemIterator($dir);

    foreach ($includes as $include) {
        if (is_dir($include) && !is_link($include)) {
            recursiveRemoveDir($include);
            continue;
        }

        @unlink($include);
    }

    return @rmdir($dir);
}
