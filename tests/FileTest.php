<?php

namespace tests;

use Nerd\Framework\Http\Request\Request;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testFileEncapsulation()
    {
        $request = new Request('/', 'GET', [], [], [
            'file1' => [
                'name' => 'foo.txt',
                'size' => 10,
                'tmp_name' => '/tmp/foo.txt',
                'error' => UPLOAD_ERR_OK
            ],
            'file2' => [
                'name' => 'foo2.txt',
                'size' => 20,
                'tmp_name' => '/tmp/foo2.txt',
                'error' => UPLOAD_ERR_OK
            ],
            'file3' => [
                'name' => 'foo3.txt',
                'size' => 40,
                'tmp_name' => '/tmp/foo3.txt',
                'error' => UPLOAD_ERR_CANT_WRITE
            ],
        ]);

        $file1 = $request->getFile('file1');

        $this->assertEquals('foo.txt', $file1->getName());
        $this->assertEquals('/tmp/foo.txt', $file1->getTempName());
        $this->assertEquals(10, $file1->getSize());

        $file2 = $request->getFile('file2');

        $this->assertEquals('foo2.txt', $file2->getName());
        $this->assertEquals('/tmp/foo2.txt', $file2->getTempName());
        $this->assertEquals(20, $file2->getSize());

        $this->assertNull($request->getFile('file3'));
    }
}
