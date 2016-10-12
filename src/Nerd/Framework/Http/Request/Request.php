<?php

namespace Nerd\Framework\Http\Request;

use Nerd\Framework\Http\RequestContract;

class Request implements RequestContract
{
    const DEFAULT_USER_AGENT = 'Nerd/1.0';
    const DEFAULT_HOST = '127.0.0.1';

    private $path;
    private $method;
    private $isSecure;

    private $query;
    private $post;
    private $files;
    private $cookies;

    private $parameters;
    private $headers;

    private static $filteredParameters = [
        "REMOTE_ADDR", "SERVER_ADDR"
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
        $this->isSecure = array_key_exists('HTTPS', $parameters)
            && $parameters['HTTPS'] !== 'off';

        $this->parameters = self::filterParameters($parameters);
        $this->headers = self::parametersToHeaders($parameters);

        $this->query = $query;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookies;
    }

    /**
     * @param $path
     * @param string $method
     * @param array $query
     * @param boolean $isSecure
     * @param string $remoteAddress
     * @return Request
     */
    public static function create(
        $path,
        $method = 'GET',
        array $query = [],
        $isSecure = false,
        $remoteAddress = self::DEFAULT_HOST
    ) {
        $pathParts = explode('?', $path, 2);

        if (sizeof($pathParts) == 2) {
            $mergedQuery = array_merge(self::queryStringToAssoc($pathParts[1]), $query);
            return self::create(
                $pathParts[0],
                $method,
                $mergedQuery,
                $remoteAddress
            );
        }

        return new self($path, $method, $query, [], [], [], [
            "REMOTE_ADDR" => $remoteAddress,
            "SERVER_ADDR" => $remoteAddress,
            "HTTP_X_REAL_IP" => $remoteAddress,
            "HTTP_USER_AGENT" => self::DEFAULT_USER_AGENT,
            "HTTPS" => $isSecure ? "on" : "off"
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
     * Convert HTTP_ parameters to headers map.
     *
     * @param $parameters
     * @return mixed
     */
    private static function parametersToHeaders($parameters)
    {
        $filtered = array_filter(array_keys($parameters), function ($key) {
            return strpos($key, 'HTTP_') === 0;
        });
        $transformKey = function ($key) {
            $parts = explode('_', $key);
            $lowerCase = array_map('strtolower', array_slice($parts, 1));
            $firstUpper = array_map('ucfirst', $lowerCase);
            return implode('-', $firstUpper);
        };

        return array_reduce($filtered, function ($acc, $key) use ($parameters, $transformKey) {
            $headerKey = $transformKey($key);

            return array_merge(
                $acc,
                [$headerKey => $parameters[$key]]
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

    public function isSecure()
    {
        return $this->isSecure;
    }

    public function hasFiles()
    {
        return sizeof($this->files) > 0;
    }

    public function getUserAgent()
    {
        return $this->getHeader('User-Agent');
    }

    public function getRemoteAddress()
    {
        return $this->getHeader('X-Real-Ip')
            ?: $this->getServerParameter('REMOTE_ADDR');
    }

    public function getServerAddress()
    {
        return $this->getServerParameter('SERVER_ADDR');
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

    public function getHeader($key, $default = null)
    {
        return array_key_exists($key, $this->headers) ? $this->headers[$key] : $default;
    }
}
