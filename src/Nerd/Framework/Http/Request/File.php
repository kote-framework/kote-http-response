<?php

namespace Nerd\Framework\Http\Request;

class File
{
    private $name;
    private $size;
    private $tempName;

    /**
     * @param $name
     * @param $size
     * @param $tempName
     */
    public function __construct($name, $size, $tempName)
    {
        $this->name = $name;
        $this->size = $size;
        $this->tempName = $tempName;
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
}
