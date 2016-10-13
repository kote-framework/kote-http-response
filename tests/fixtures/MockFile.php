<?php

namespace tests\fixtures;

use Nerd\Framework\Http\Request\File;

class MockFile extends File
{
    /**
     * @param $file
     * @return bool
     */
    protected function isUploadedFile($file)
    {
        return file_exists($file);
    }

    /**
     * @param $file
     * @param $destination
     * @return bool
     */
    protected function moveUploadedFile($file, $destination)
    {
        if (!is_dir(pathinfo($destination, PATHINFO_DIRNAME))) {
            return false;
        }
        return copy($file, $destination);
    }
}
