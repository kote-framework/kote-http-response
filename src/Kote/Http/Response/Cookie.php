<?php
/**
 * @author Roman Gemini <roman_gemini@ukr.net>
 * @date 16.05.16
 * @time 22:11
 */

namespace Kote\Http\Response;


class Cookie
{
    private $name;
    private $value;
    private $expire;
    private $path;
    private $domain;
    private $secure;
    private $http;

    /**
     * Cookie constructor.
     *
     * @param string $name
     * @param mixed $value
     * @param int $expire
     * @param string|null $path
     * @param string|null $domain
     * @param bool $secure
     * @param bool $http
     */
    public function __construct($name, $value, $expire = 0, $path = null, $domain = null, $secure = false, $http = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->expire = $expire;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->http = $http;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getExpire()
    {
        return $this->expire;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getSecure()
    {
        return $this->secure;
    }

    public function getHttp()
    {
        return $this->http;
    }

    /**
     * Sends cookie to client.
     */
    public function send()
    {
        setcookie(
            $this->getName(),
            $this->getValue(),
            $this->getExpire(),
            $this->getPath(),
            $this->getDomain(),
            $this->getSecure(),
            $this->getHttp()
        );
    }
}