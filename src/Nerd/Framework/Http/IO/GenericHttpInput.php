<?php

namespace Nerd\Framework\Http\IO;

use Nerd\Framework\Http\InputContract;
use Nerd\Framework\Http\RequestContract;
use Nerd\Framework\Http\Request\Request;

class GenericHttpInput implements InputContract
{
    /**
     * Get HTTP Request Object.
     *
     * @return RequestContract
     */
    public function getRequestObject()
    {
        return new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
    }

    /**
     * Get HTTP Request Body as string.
     *
     * @return string
     */
    public function getRequestBody()
    {
        return stream_get_contents($this->getRequestBodyStream());
    }

    /**
     * Get HTTP Request Body Stream.
     *
     * @return resource
     */
    public function getRequestBodyStream()
    {
        return fopen("php://input", "rb");
    }
}
