<?php

namespace Nerd\Framework\Http\Request;

use Nerd\Framework\Http\RequestContract;

class Request implements RequestContract
{
    private $server;
    private $query;
    private $post;
    private $files;
    private $cookies;

    private $path;

    public function __construct(array $server, array $query, array $post, array $files, array $cookies)
    {
        $this->server = $server;
        $this->query = $query;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookies;

        $parsedPath = parse_url($this->server["REQUEST_URI"], PHP_URL_PATH);

        $this->path = self::trimPath($parsedPath);
    }

    private static function trimPath($path)
    {
        return ltrim(urldecode($path), "/") ?: "/";
    }

    public function getMethod()
    {
        return $this->getServerParameter('REQUEST_METHOD');
    }

    public function isMethod($method)
    {
        return 0 === strcasecmp($method, $this->getMethod());
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getServerParameter($key, $default = null)
    {
        return array_key_exists($key, $this->server) ? $this->server[$key] : $default;
    }

    public function getQueryParameter($key, $default = null)
    {
        return array_key_exists($key, $this->query) ? $this->query[$key] : $default;
    }

    public function getPostParameter($key, $default = null)
    {
        return array_key_exists($key, $this->post) ? $this->post[$key] : $default;
    }

    public function getCookie($key, $default = null)
    {
        return array_key_exists($key, $this->cookies) ? $this->cookies[$key] : $default;
    }
}
