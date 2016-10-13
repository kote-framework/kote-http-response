<?php

namespace tests;

use Nerd\Framework\Http\IO\GenericHttpInput;
use PHPUnit\Framework\TestCase;

class GenericIOTest extends TestCase
{
    public function testMakeInput()
    {
        $input = new GenericHttpInput();
        $request = $input->getRequest();

        $this->assertTrue($request->isMethod('GET'));
        $this->assertEquals('/', $request->getPath());
        $this->assertEquals('Test/1.0', $request->getUserAgent());
        $this->assertEquals('2.3.4.5', $request->getRemoteAddress());
        $this->assertEquals('4.3.2.1', $request->getServerAddress());

        $this->assertEquals('cookieValue', $request->getCookie('cookieName'));
        $this->assertEquals('postValue', $request->getPostParameter('postName'));
    }
}
