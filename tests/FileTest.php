<?php

namespace tests;

use Nerd\Framework\Http\Request\File;
use Nerd\Framework\Http\Request\Request;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use tests\fixtures\MockFile;

class FileTest extends TestCase
{
    private $uploadContent = "Random file contents.";

    private $vfs;

    private $uploadFile;

    public function setUp()
    {
        $this->vfs = vfsStream::setup('upload');
        $this->uploadFile = vfsStream::newFile('load.me')
                           ->withContent($this->uploadContent)
                           ->at($this->vfs)
                           ->url();
    }

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

    public function testFile()
    {
        $file = new MockFile(
            pathinfo($this->uploadFile, PATHINFO_BASENAME),
            filesize($this->uploadFile),
            $this->uploadFile
        );

        $this->assertEquals('load.me', $file->getName());
        $this->assertEquals(filesize($this->uploadFile), $file->getSize());
        $this->assertEquals($this->uploadFile, $file->getTempName());

        $this->assertFalse($file->isSaved());
        $this->assertTrue($file->isOk());

        return $file;
    }

    /**
     * @depends testFile
     * @param File $file
     * @return File
     */
    public function testSaveFile(File $file)
    {
        $clonedFile = clone $file;

        $contents = stream_get_contents($clonedFile->getStream());
        $saveTo = vfsStream::newFile('save.me')->at($this->vfs);

        $clonedFile->saveAs($saveTo->url());
        $this->assertTrue($clonedFile->isSaved());
        $this->assertEquals($contents, $saveTo->getContent());

        return $clonedFile;
    }

    /**
     * @depends testFile
     * @expectedException \Exception
     * @param File $file
     */
    public function testSaveError(File $file)
    {
        $clonedFile = clone $file;

        $clonedFile->saveAs($this->vfs->url());
    }

    /**
     * @depends testSaveFile
     * @expectedException \Exception
     * @param File $file
     */
    public function testSaveAgain(File $file)
    {
        $dir = vfsStream::setup('root');
        $saveFile = vfsStream::newFile('save.again')->at($dir);

        $file->saveAs($saveFile->url());
    }
}
