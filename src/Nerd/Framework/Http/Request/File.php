<?php

namespace Nerd\Framework\Http\Request;

class File implements FileContract
{
    private $name;
    private $size;
    private $tempName;
    private $error;

    /**
     * @param $name
     * @param $size
     * @param $tempName
     * @param int $error
     */
    public function __construct($name, $size, $tempName, $error = UPLOAD_ERR_OK)
    {
        $this->name = $name;
        $this->size = $size;
        $this->tempName = $tempName;
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getTempName()
    {
        return $this->tempName;
    }

    /**
     * @param $path
     */
    public function saveAs($path)
    {
        if (move_uploaded_file($this->getTempName(), $path)) {
            $this->tempName = $path;
        }
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return fopen($this->getTempName(), 'rb');
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return $this->error === UPLOAD_ERR_OK;
    }

    /**
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }
}
