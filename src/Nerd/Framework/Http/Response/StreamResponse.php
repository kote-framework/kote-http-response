<?php

namespace Nerd\Framework\Http\Response;

class StreamResponse extends Response
{
    const PIPE_BUFFER_SIZE = 4096;

    private $stream;

    public function __construct($stream = null, $statusCode = StatusCode::HTTP_OK)
    {
        if ("stream" != get_resource_type($stream)) {
            throw new \InvalidArgumentException("Invalid stream handle passed into response.");
        }

        $this->setStream($stream);
        $this->setStatusCode($statusCode);
    }

    public function setStream($fp)
    {
        $this->stream = $fp;

        return $this;
    }

    public function renderContent()
    {
        while ($data = fread($this->stream, self::PIPE_BUFFER_SIZE)) {
            echo $data;
            flush();
        }
    }
}
