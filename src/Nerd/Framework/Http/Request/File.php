<?php

namespace Nerd\Framework\Http\Request;

class File implements FileContract
{
    private $name;
    private $size;
    private $tempName;
    private $error;

    private $saved = false;

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
     * @param string $path
     * @throws \Exception
     */
    public function saveAs($path)
    {
        if ($this->saved) {
            throw new \Exception("File \"{$this->getName()}\" already saved to \"{$this->getTempName()}\"");
        }
        if (!$this->isUploadedFile($this->getTempName())) {
            throw new \Exception("File \"{$this->getName()}\" is not valid uploaded file");
        }
        if ($this->moveUploadedFile($this->getTempName(), $path)) {
            $this->tempName = $path;
            $this->saved = true;
        } else {
            throw new \Exception("File {$this->getName()} could not be saved");
        }
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return fopen($this->getTempName(), 'r');
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

    /**
     * @return bool
     */
    public function isSaved()
    {
        return $this->saved;
    }

    /**
     * @param $file
     * @return bool
     */
    protected function isUploadedFile($file)
    {
        return is_uploaded_file($file);
    }

    /**
     * @param $file
     * @param $destination
     * @return bool
     */
    protected function moveUploadedFile($file, $destination)
    {
        return move_uploaded_file($file, $destination);
    }
}
