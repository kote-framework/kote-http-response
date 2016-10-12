<?php

namespace tests\fixtures;

use Nerd\Framework\Http\Request\File;

class MockFile extends File
{
    protected function isUploadedFile($file)
    {
        return file_exists($file);
    }

    protected function moveUploadedFile($file, $destination)
    {
        return copy($file, $destination);
    }
}
