<?php

namespace tests;

use Nerd\Framework\Http\IO\OutputContract;
use Nerd\Framework\Http\Response\ResponseContract;
use PHPUnit\Framework\TestCase;

use Nerd\Framework\Http\Response;

class ResponseTest extends TestCase
{
    public function testEmptyResponse()
    {
        $response = new Response\EmptyResponse();

        $output = $this->createMock(OutputContract::class);
        $output->expects($this->never())->method('sendData');

        $this->assertInstanceOf(ResponseContract::class, $response);

        $response->render($output);
    }

    public function testPlainResponse()
    {
        $response = new Response\PlainResponse('hello, ');
        $response->write('world!');
        $this->assertEquals('hello, world!', $response->getContent());

        $output = $this->createMock(OutputContract::class);
        $output->expects($this->once())->method('sendData')->with($this->equalTo('hello, world!'));

        $this->assertInstanceOf(ResponseContract::class, $response);

        $response->render($output);
    }

    public function testStreamResponse()
    {
        $stream = fopen('data://text/plain,random data', 'r');

        $response = new Response\StreamResponse($stream);

        $output = $this->createMock(OutputContract::class);
        $output->expects($this->once())->method('sendData')->with($this->equalTo('random data'));

        $this->assertInstanceOf(ResponseContract::class, $response);

        $response->render($output);

        $response->close();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadStream()
    {
        new Response\StreamResponse(null);
    }

    public function testJsonResponse()
    {
        $json = ['test' => ['hello', 'foo'], 'bar' => 'baz'];
        $response = new Response\JsonResponse($json);
        $this->assertEquals($json, $response->getData());

        $output = $this->createMock(OutputContract::class);
        $output->expects($this->once())->method('sendData')->with($this->equalTo(json_encode($json)));

        $this->assertInstanceOf(ResponseContract::class, $response);

        $response->render($output);
    }

    public function testCallbackResponse()
    {
        $output = $this->createMock(OutputContract::class);
        $output->expects($this->once())->method('sendData')->with($this->equalTo('callback ok'));

        $callable = function (OutputContract $output) use (&$called) {
            $output->sendData('callback ok');
        };

        $response = new Response\CallbackResponse($callable);

        $response->render($output);
    }

    public function testGenericViewResponse()
    {
        Response\GenericViewResponse::setViewDirectoryPrefix(__DIR__ . '/fixtures/view');

        $output = $this->createMock(OutputContract::class);
        $output->expects($this->once())->method('sendData')->with($this->equalTo('<h1>Hello</h1><p>bar</p>'));

        $response = new Response\GenericViewResponse('foo.view.php');

        $response->bindVariable('foo', 'bar');

        $response->render($output);

        $this->assertEquals('foo.view.php', $response->getViewFileName());
    }

    public function testCookieSend()
    {
        $response = new Response\EmptyResponse();

        $cookie = $this->createMock(Response\CookieContract::class);

        $output = $this->createMock(OutputContract::class);
        $output->expects($this->once())->method('sendCookie')->with($this->equalTo($cookie));

        $response->addCookie($cookie);

        $response->render($output);
    }
}
