<?php

namespace Nerd\Framework\Http\Response;

use Nerd\Framework\Http\OutputContract;
use Nerd\Framework\Http\RequestContract;
use Nerd\Framework\Http\ResponseContract;

abstract class Response implements ResponseContract
{
    const DEFAULT_CONTENT_TYPE = "text/html";
    const DEFAULT_CHARSET = "utf-8";

    const CONTENT_DISPOSITION_HEADER = "Content-Disposition";
    const CONTENT_LENGTH_HEADER = "Content-Length";
    const CONTENT_TYPE_HEADER = "Content-Type";

    private $statusCode = StatusCode::HTTP_OK;

    /**
     * Array of headers to be sent.
     *
     * @var array
     */
    private $headers = [];

    /**
     * Array of Cookies to be sent.
     *
     * @var Cookie[] $cookies
     */
    private $cookies = [];

    /**
     * File name assigned to response.
     *
     * @var null|string
     */
    private $fileName = null;

    /**
     * Is this response an attachment?
     *
     * @var bool
     */
    private $isAttachment = false;

    /**
     * Content type for this response.
     *
     * @var string
     */
    private $contentType = self::DEFAULT_CONTENT_TYPE;

    /**
     * Characters set.
     *
     * @var string
     */
    private $charset = self::DEFAULT_CHARSET;

    /**
     * Content length of this response.
     *
     * @var null|int
     */
    private $contentLength = null;

    /**
     * Server protocol version.
     *
     * @var string
     */
    private $serverProtocol = null;

    /**
     * @var bool
     */
    private $shouldRenderContent = true;

    /**
     * @return string
     */
    public function getServerProtocol()
    {
        return $this->serverProtocol;
    }

    /**
     * @param string $serverProtocol
     */
    public function setServerProtocol($serverProtocol)
    {
        $this->serverProtocol = $serverProtocol;
    }

    protected abstract function renderContent(OutputContract $output);

    /**
     * @param RequestContract $request
     * @return void
     */
    public function prepare(RequestContract $request)
    {
        if (is_null($this->getServerProtocol())) {
            $this->setServerProtocol($_SERVER['SERVER_PROTOCOL']);
        }

        if ($request->isMethod('HEAD')) {
            $this->shouldRenderContent = false;
        }
    }

    public function render(OutputContract $output)
    {
        $this->prepareHeaders();
        $this->sendStatusCode($output);
        $this->sendHeaders($output);

        if ($this->shouldRenderContent) {
            $this->renderContent($output);
        }
    }

    private function prepareHeaders()
    {
        if ($this->getFileName()) {
            $this->addHeader(self::CONTENT_DISPOSITION_HEADER, "filename*=UTF-8''" . $this->fileName);
        }

        if ($this->isAttachment) {
            $this->addHeader(self::CONTENT_DISPOSITION_HEADER, "attachment");
        }

        if (!is_null($this->contentLength)) {
            $this->addHeader(self::CONTENT_LENGTH_HEADER, $this->contentLength);
        }

        $this->addHeader(self::CONTENT_TYPE_HEADER, $this->contentType ?: self::DEFAULT_CONTENT_TYPE);

        $this->addHeader(self::CONTENT_TYPE_HEADER, "charset={$this->charset}");
    }

    private function sendHeaders(OutputContract $output)
    {
        if ($output->isHeadersSent()) {
            return;
        }

        foreach ($this->cookies as $cookie) {
            $output->sendCookie($cookie);
        }

        foreach ($this->headers as $name => $value) {
            $output->sendHeader($name.": ".implode("; ", $value));
        }
    }

    private function sendStatusCode(OutputContract $output)
    {
        $output->sendHeader($this->getStatusText($this->statusCode));
    }

    public function getStatusText($statusCode)
    {
        switch ($statusCode) {
            case 100: $text = 'Continue'; break;
            case 101: $text = 'Switching Protocols'; break;
            case 200: $text = 'OK'; break;
            case 201: $text = 'Created'; break;
            case 202: $text = 'Accepted'; break;
            case 203: $text = 'Non-Authoritative Information'; break;
            case 204: $text = 'No Content'; break;
            case 205: $text = 'Reset Content'; break;
            case 206: $text = 'Partial Content'; break;
            case 300: $text = 'Multiple Choices'; break;
            case 301: $text = 'Moved Permanently'; break;
            case 302: $text = 'Moved Temporarily'; break;
            case 303: $text = 'See Other'; break;
            case 304: $text = 'Not Modified'; break;
            case 305: $text = 'Use Proxy'; break;
            case 400: $text = 'Bad Request'; break;
            case 401: $text = 'Unauthorized'; break;
            case 402: $text = 'Payment Required'; break;
            case 403: $text = 'Forbidden'; break;
            case 404: $text = 'Not Found'; break;
            case 405: $text = 'Method Not Allowed'; break;
            case 406: $text = 'Not Acceptable'; break;
            case 407: $text = 'Proxy Authentication Required'; break;
            case 408: $text = 'Request Time-out'; break;
            case 409: $text = 'Conflict'; break;
            case 410: $text = 'Gone'; break;
            case 411: $text = 'Length Required'; break;
            case 412: $text = 'Precondition Failed'; break;
            case 413: $text = 'Request Entity Too Large'; break;
            case 414: $text = 'Request-URI Too Large'; break;
            case 415: $text = 'Unsupported Media Type'; break;
            case 500: $text = 'Internal Server Error'; break;
            case 501: $text = 'Not Implemented'; break;
            case 502: $text = 'Bad Gateway'; break;
            case 503: $text = 'Service Unavailable'; break;
            case 504: $text = 'Gateway Time-out'; break;
            case 505: $text = 'HTTP Version not supported'; break;
            default:
                throw new \InvalidArgumentException('Unknown HTTP Status Code "' . htmlentities($statusCode) . '"');
                break;
        }

        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

        return $protocol . ' ' . $statusCode . ' ' . $text;
    }

    /**
     * @param int $statusCode
     * @return Response
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return Response
     */
    public function addHeader($name, $value)
    {
        if (!isset($this->headers[$name])) {
            $this->headers[$name] = [];
        }
        $this->headers[$name][] = $value;
        return $this;
    }

    /**
     * @param Cookie $cookie
     * @return Response
     */
    public function addCookie(Cookie $cookie)
    {
        $this->cookies[] = $cookie;
        return $this;
    }

    /**
     * @param null $fileName
     * @return Response
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param boolean $isAttachment
     * @return Response
     */
    public function setAttachment($isAttachment)
    {
        $this->isAttachment = $isAttachment;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAttachment()
    {
        return $this->isAttachment;
    }

    /**
     * @param string $contentType
     * @return Response
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @param string $charset
     * @return Response
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * @param null $contentLength
     * @return Response
     */
    public function setContentLength($contentLength)
    {
        $this->contentLength = $contentLength;
        return $this;
    }
}
