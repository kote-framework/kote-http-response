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

        $this->assertEquals('127.0.0.1', $request->getRemoteAddress());
        $this->assertEquals(Request::USER_AGENT, $request->getUserAgent());
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

        $request2 = new Request('/', 'GET', [], [], [], [], [
            'HTTP_X_REAL_IP' => '1.2.3.4',
            'REMOTE_ADDR' => '5.6.7.8'
        ]);

        $this->assertEquals('1.2.3.4', $request2->getRemoteAddress());
    }
}
