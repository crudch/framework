<?php

namespace Crudch\Http;

/**
 * Class Response
 *
 * @package Crudch
 */
class Response
{
    protected $data;

    /**
     * @param mixed $data
     *
     * @return Response
     */
    public function setData($data): Response
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param string $url
     * @param int    $code
     *
     * @return Response
     */
    public function redirect($url, $code = 302): Response
    {
        $this->setHeader('Location: ' . $url, $code);

        return $this;
    }

    /**
     * @return Response
     */
    public function back(): Response
    {
        $url = !empty($_SERVER['HTTP_REFERER']) && false !== filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL)
            ? $_SERVER['HTTP_REFERER'] : '/';

        return $this->redirect($url);
    }

    /**
     * @param mixed $data
     * @param int   $code
     *
     * @return Response
     */
    public function json($data, $code = 200): Response
    {
        try {
            $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } catch (\JsonException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }

        $this->setHeader('Content-Type: application/json;charset=utf-8', $code);

        return $this->setData($json);
    }

    /**
     * Записывает данные в сессию
     *
     * @param $name
     * @param $value
     *
     * @return Response
     */
    public function withSession($name, $value = null): Response
    {
        foreach (\is_array($name) ? $name : [$name => $value] as $key => $item) {
            $_SESSION[$key] = $item;
        }

        return $this;
    }

    /**
     * @param array  $data [name => value]
     * @param bool   $secure
     * @param int    $expire
     * @param string $path
     * @param string $domain
     *
     * @return Response
     */
    public function withCookie(array $data, $secure = false, $expire = 0x7FFFFFFF, $path = '/', $domain = ''): Response
    {
        foreach ($data as $key => $value) {
            setcookie($key, $value, $expire, $path, $domain, $secure, true);
        }

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function withInputs(Request $request): Response
    {
        return $this->withSession($request->post());
    }

    /**
     * @param array $header
     * @param int   $code
     * @param bool  $replace
     *
     * @return Response
     */
    public function withHeaders(array $header, $code = null, $replace = false): Response
    {
        foreach ($header as $key => $value) {
            $this->setHeader($key . ': ' . $value, $code, $replace);
        }

        return $this;
    }

    /**
     * @param int $code
     *
     * @return Response
     */
    public function withCode($code): Response
    {
        http_response_code($code);

        return $this;
    }

    /**
     * Устанавливает заголовок
     *
     * @param string $header
     * @param int    $code
     * @param bool   $replace
     */
    protected function setHeader($header, $code = null, $replace = true): void
    {
        header($header, $replace, $code);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->data;
    }
}
