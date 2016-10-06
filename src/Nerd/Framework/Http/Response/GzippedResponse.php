<?php

namespace Nerd\Framework\Http\Response;

class GzippedResponse extends Response
{
    const CONTENT_CHUNK_SIZE = 4096;

    /**
     * @var ResponseContract
     */
    private $response;

    /**
     * @var int
     */
    private $chunkSize;

    /**
     * GzippedResponse constructor.
     *
     * @param Response $response
     * @param int $chunkSize
     */
    public function __construct(Response $response, $chunkSize = self::CONTENT_CHUNK_SIZE)
    {
        $this->response = $response;
        $this->chunkSize = $chunkSize;
    }

    public function renderContent()
    {
        ob_start("ob_gzhandler", $this->chunkSize);
        $this->response->renderContent();
        ob_flush();
    }

    /**
     * Do not compress already compressed content.
     *
     * @return $this
     */
    public function gzip() {
        return $this;
    }
}