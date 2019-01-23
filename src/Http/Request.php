<?php

namespace Crudch\Http;

/**
 * Class Request
 *
 * @package Crudch
 */
class Request
{
    /**
     * @var array
     */
    protected $get;

    /**
     * @var array
     */
    protected $post;

    /**
     * @var array
     */
    protected $files;

    /**
     * @var array
     */
    protected $cookie;

    /**
     * @var array
     */
    protected $all;

    /**
     * @var array
     */
    protected $headers;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->cookie = $_COOKIE;
    }

    /**
     * @param mixed $params
     *
     * @return mixed
     */
    public function get($params = null)
    {
        return $this->getData($this->get, $params);
    }

    /**
     * @param mixed $params
     *
     * @return mixed
     */
    public function post($params = null)
    {
        return $this->getData($this->post, $params);
    }

    /**
     * @param mixed $params
     *
     * @return mixed
     */
    public function cookie($params = null)
    {
        return $this->getData($this->cookie, $params);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        if (null !== $this->all) {
            return $this->all;
        }

        return $this->all = array_merge($this->post(), $this->get());
    }

    /**
     * @param mixed $params
     *
     * @return string|array|null
     */
    public function input($params = null)
    {
        $all = $this->all();

        return $this->getData($all, $params);
    }

    /**
     * @deprecated тоже самое, что и get(),post(), input()
     *
     * @param array|string $params
     *
     * @return array
     */
    public function only($params): array
    {
        return array_intersect_key($this->all(), array_flip((array)$params));
    }

    /**
     * @param array|string $params
     *
     * @return array
     */
    public function except($params): array
    {
        $params = (array)$params;

        return array_filter($this->all(), function ($key) use (&$params) {
            return !\in_array($key, $params, true);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name): bool
    {
        return array_key_exists($name, $this->all());
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public function hasFile($filename): bool
    {
        return !empty($this->files[$filename]) && UPLOAD_ERR_NO_FILE !== $this->files[$filename]['error'];
    }

    /**
     * @param string $filename
     *
     * @return array|null
     */
    public function file($filename): ?array
    {
        return $this->files[$filename] ?? null;
    }

    /**
     * @param string|array $name
     * @param string|null  $value
     *
     * @return Request
     */
    public function setAttributes($name, $value = null): Request
    {
        $this->all = null;

        foreach (\is_array($name) ? $name : [$name => $value] as $key => $item) {
            \is_string($key) && $this->get[$key] = $item;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function uri(): string
    {
        $uri = ltrim($_SERVER['REQUEST_URI'], '/');

        return (false !== $pos = strpos($uri, '?')) ? substr($uri, 0, $pos) : $uri;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return bool
     */
    public function ajax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * @return mixed [ip address or false]
     */
    public function clientIp()
    {
        return filter_var(
            $_SERVER['REMOTE_ADDR'],
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * @return int
     */
    public function clientIp2long(): int
    {
        return (int)sprintf('%u', ip2long($this->clientIp()));
    }

    /**
     * @param $data
     * @param $params
     *
     * @return array|string|null
     */
    protected function getData(&$data, $params)
    {
        if (null === $params) {
            return $data;
        }

        if (\is_string($params)) {
            return $data[$params] ?? null;
        }

        $result = [];

        foreach ((array)$params as $arg) {
            $result[$arg] = $data[$arg] ?? null;
        }

        return $result;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->setAttributes($name, $value);
    }

    /**
     * @param $name
     *
     * @return array|string|null
     */
    public function __get($name)
    {
        return $this->input($name);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }
}
