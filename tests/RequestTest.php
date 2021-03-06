<?php

namespace tests;

use Nerd\Framework\Http\Request\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testCreateGetRequest()
    {
        $request = Request::create('/');

        $this->assertInstanceOf(Request::class, $request);

        $this->assertEquals('/', $request->getPath());
        $this->assertEquals('GET', $request->getMethod());
        $this->assertTrue($request->isMethod('GET'));
        $this->assertFalse($request->isSecure());
        $this->assertFalse($request->hasFiles());

        $this->assertEquals(Request::DEFAULT_HOST, $request->getRemoteAddress());
        $this->assertEquals(Request::DEFAULT_USER_AGENT, $request->getUserAgent());
    }

    public function testCreatePostRequest()
    {
        $request = Request::create('/', 'POST');

        $this->assertInstanceOf(Request::class, $request);

        $this->assertEquals('/', $request->getPath());
        $this->assertEquals('POST', $request->getMethod());
        $this->assertTrue($request->isMethod('POST'));
    }

    public function testCreateFromQueryString()
    {
        $request = Request::create('/foo?bar=baz&id=13&no&yes=');

        $this->assertInstanceOf(Request::class, $request);

        $this->assertEquals('baz', $request->getQueryParameter('bar'));
        $this->assertEquals('13', $request->getQueryParameter('id'));
        $this->assertEquals(null, $request->getQueryParameter('no', '10'));
        $this->assertEquals('', $request->getQueryParameter('yes'));
    }

    public function testQueryOverride()
    {
        $request = Request::create('/foo?bar=baz&id=13', 'GET', ['bar' => 'over']);

        $this->assertInstanceOf(Request::class, $request);

        $this->assertEquals('over', $request->getQueryParameter('bar'));
        $this->assertEquals('13', $request->getQueryParameter('id'));
    }

    public function testRemoteAddress()
    {
        $request1 = new Request('/', 'GET', [], [], [], [], [
            'REMOTE_ADDR' => '1.2.3.4'
        ]);

        $this->assertEquals('1.2.3.4', $request1->getRemoteAddress());

        $request2 = new Request('/', 'GET', [], [], [], [], [
            'HTTP_X_REAL_IP' => '1.2.3.4'
        ]);

        $this->assertEquals('1.2.3.4', $request2->getRemoteAddress());

        $request3 = new Request('/', 'GET', [], [], [], [], [
            'HTTP_X_REAL_IP' => '1.2.3.4',
            'REMOTE_ADDR' => '5.6.7.8'
        ]);

        $this->assertEquals('1.2.3.4', $request3->getRemoteAddress());

        $request4 = Request::create('/', 'GET', [], false, '6.7.8.9');

        $this->assertEquals('6.7.8.9', $request4->getRemoteAddress());
    }

    public function testServerAddress()
    {
        $request1 = Request::create('/');

        $this->assertEquals(Request::DEFAULT_HOST, $request1->getServerAddress());

        $request2 = Request::create('/', 'GET', [], false, '6.7.8.9');

        $this->assertEquals('6.7.8.9', $request2->getServerAddress());
    }

    public function testIsSecureOption()
    {
        $request = Request::create('/', 'GET', [], true);

        $this->assertTrue($request->isSecure());
    }

    public function testParametersToHeaders()
    {
        $parameters = [
            'REMOTE_ADDR' => '1.2.3.4',
            'OTHER_THING' => 'value',
            'HTTP_FIRST_HEADER' => 'first',
            'HTTP_SECOND_HEADER' => 'second',
            'IS_NOT_HTTP_HEADER' => 'not',
            'HTTP_X_REAL_IP' => '2.3.4.5'
        ];

        $request = new Request('/', 'GET', [], [], [], [], $parameters);

        $this->assertEquals('first', $request->getHeader('First-Header'));
        $this->assertEquals('second', $request->getHeader('Second-Header'));
        $this->assertEquals('2.3.4.5', $request->getHeader('X-Real-Ip'));
    }

    public function testPostParametersAndCookies()
    {
        $request = new Request('/', 'GET', [], ["postName" => "postValue"], [], ["cookieName" => "cookieValue"]);

        $this->assertEquals('cookieValue', $request->getCookie('cookieName'));
        $this->assertEquals('postValue', $request->getPostParameter('postName'));
    }
}
