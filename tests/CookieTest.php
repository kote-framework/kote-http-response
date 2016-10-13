<?php

namespace tests;

use Nerd\Framework\Http\Response\Cookie;
use Nerd\Framework\Http\Response\RawCookie;
use PHPUnit\Framework\TestCase;

class CookieTest extends TestCase
{
    public function testCookie()
    {
        $cookie = new Cookie('name', 'value', 10, '/', '.domain.com', true, true);

        $this->assertEquals('name', $cookie->getName());
        $this->assertEquals('value', $cookie->getValue());
        $this->assertEquals(10, $cookie->getExpire());
        $this->assertEquals('/', $cookie->getPath());
        $this->assertEquals('.domain.com', $cookie->getDomain());
        $this->assertTrue($cookie->isSecure());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertFalse($cookie->isRaw());
    }

    public function testRawCookie()
    {
        $cookie = new RawCookie('name', 'value', 10, '/', '.domain.com', true, true);

        $this->assertEquals('name', $cookie->getName());
        $this->assertEquals('value', $cookie->getValue());
        $this->assertEquals(10, $cookie->getExpire());
        $this->assertEquals('/', $cookie->getPath());
        $this->assertEquals('.domain.com', $cookie->getDomain());
        $this->assertTrue($cookie->isSecure());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertTrue($cookie->isRaw());
    }

    public function testCookieToString()
    {
        $cookie = new RawCookie('name', 'value', 10, '/', '.domain.com', true, true);
        $expected = "Set-Cookie: name=value; Expires=10; Path=/; Domain=.domain.com; Secure; HttpOnly";
        $this->assertEquals($expected, (string) $cookie);
    }
}
