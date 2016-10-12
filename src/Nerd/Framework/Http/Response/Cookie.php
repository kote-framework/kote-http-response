<?php

namespace Nerd\Framework\Http\Response;

class Cookie implements CookieContract
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
    public function __construct(
        $name,
        $value,
        $expire = 0,
        $path = null,
        $domain = null,
        $secure = false,
        $http = false
    ) {
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

    public function isSecure()
    {
        return $this->secure;
    }

    public function isHttpOnly()
    {
        return $this->http;
    }

    public function isRaw()
    {
        return false;
    }

    public function __toString()
    {
        $string  = 'Set-Cookie: ';
        $string .= $this->getName() . '=' . $this->getValue();

        if ($this->getExpire()) {
            $string .= '; Expires=' . $this->getExpire();
        }

        if ($this->getPath()) {
            $string .= '; Path=' . $this->getPath();
        }

        if ($this->getDomain()) {
            $string .= '; Domain=' . $this->getDomain();
        }

        if ($this->isSecure()) {
            $string .= '; Secure';
        }

        if ($this->isHttpOnly()) {
            $string .= '; HttpOnly';
        }

        return $string;
    }
}
