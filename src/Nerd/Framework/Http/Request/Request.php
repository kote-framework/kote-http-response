<?php

namespace Nerd\Framework\Http\Request;

use Nerd\Framework\Http\RequestContract;

class Request implements RequestContract
{
    const USER_AGENT = 'Nerd/1.0';

    private $path;
    private $method;

    private $query;
    private $post;
    private $files;
    private $cookies;

    private $parameters;

    private static $filteredParameters = [
        "REMOTE_ADDR",
        "HTTP_X_REAL_IP",
        "HTTP_USER_AGENT"
    ];

    public function __construct(
        $path,
        $method = 'GET',
        array $query = [],
        array $post = [],
        array $files = [],
        array $cookies = [],
        array $parameters = []
    ) {
        $this->method = $method;

        $this->path = self::normalizePath($path);
        $this->parameters = self::filterParameters($parameters);

        $this->query = $query;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookies;
    }

    /**
     * @param $path
     * @param string $method
     * @param array $query
     * @param string $remoteAddress
     * @return Request
     */
    public static function create(
        $path,
        $method = 'GET',
        array $query = [],
        $remoteAddress = '127.0.0.1'
    ) {
        $pathParts = explode('?', $path, 2);

        if (sizeof($pathParts) == 2) {
            return self::create(
                $pathParts[0],
                $method,
                array_merge(self::queryStringToAssoc($pathParts[1]), $query),
                $remoteAddress
            );
        }

        return new self($path, $method, $query, [], [], [], [
            "REMOTE_ADDR" => $remoteAddress,
            "HTTP_X_REAL_IP" => $remoteAddress,
            "HTTP_USER_AGENT" => self::USER_AGENT
        ]);
    }

    private static function normalizePath($path)
    {
        return ltrim(urldecode($path), "/") ?: "/";
    }

    private static function queryStringToAssoc($queryString)
    {
        $parts = explode('&', $queryString);

        return array_reduce($parts, function ($acc, $part) {
            $keyVal = explode('=', $part, 2);
            return array_merge(
                $acc,
                sizeof($keyVal) == 2
                    ? [$keyVal[0] => urldecode($keyVal[1])]
                    : [$keyVal[0] => null]
            );
        }, []);
    }

    /**
     * @param array $parameters
     * @return array
     */
    private static function filterParameters(array $parameters)
    {
        return array_filter($parameters, function ($key) {
            return in_array($key, self::$filteredParameters);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function isMethod($method)
    {
        return 0 === strcasecmp($method, $this->getMethod());
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getUserAgent()
    {
        return $this->getServerParameter('HTTP_USER_AGENT');
    }

    public function getRemoteAddress()
    {
        return $this->getServerParameter('HTTP_X_REAL_IP') ?: $this->getServerParameter('REMOTE_ADDR');
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

    public function getServerParameter($key, $default = null)
    {
        return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
    }
}
