<?php

namespace Nerd\Framework\Http\Response;

use Nerd\Framework\Http\IO\OutputContract;

class StreamResponse extends Response
{
    private $stream;

    public function __construct($stream = null, $statusCode = StatusCode::HTTP_OK)
    {
        if (!is_resource($stream) || "stream" != get_resource_type($stream)) {
            throw new \InvalidArgumentException("Invalid stream handle passed into response.");
        }

        $this->setStream($stream);
        $this->setStatusCode($statusCode);
    }

    private function setStream($fp)
    {
        $this->stream = $fp;

        return $this;
    }

    protected function renderContent(OutputContract $output)
    {
        $output->sendData($this->stream);
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        fclose($this->stream);
    }
}
