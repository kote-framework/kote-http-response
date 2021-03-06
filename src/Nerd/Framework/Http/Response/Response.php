<?php

namespace Nerd\Framework\Http\Response;

use Nerd\Framework\Http\IO\OutputContract;
use Nerd\Framework\Http\Request\RequestContract;

abstract class Response implements BaseResponseContract
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
     * @var CookieContract[] $cookies
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
     * @return int|null
     */
    public function getContentLength()
    {
        return $this->contentLength;
    }

    /**
     * @param int $length
     */
    public function setContentLength($length)
    {
        $this->contentLength = $length;
    }

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

    abstract protected function renderContent(OutputContract $output);

    /**
     * @param RequestContract $request
     * @return void
     */
    public function prepare(RequestContract $request)
    {
        if (is_null($this->getServerProtocol())) {
            $this->setServerProtocol(
                $request->getServerParameter('SERVER_PROTOCOL')
            );
        }

        if ($request->isMethod('HEAD')) {
            $this->shouldRenderContent = false;
        }
    }

    /**
     * @param OutputContract $output
     * @return void
     */
    public function render(OutputContract $output)
    {
        $this->prepareHeaders();
        $this->sendStatusCode($output);
        $this->sendHeaders($output);

        if ($this->shouldRenderContent) {
            $this->renderContent($output);
        }
    }

    /**
     * return void
     */
    protected function prepareHeaders()
    {
        if ($this->isAttachment) {
            $this->addHeader(self::CONTENT_DISPOSITION_HEADER, "attachment");
        }

        if ($this->getFileName()) {
            $this->addHeader(self::CONTENT_DISPOSITION_HEADER, "filename*=UTF-8''" . $this->fileName);
        }

        if (!is_null($this->contentLength)) {
            $this->addHeader(self::CONTENT_LENGTH_HEADER, $this->contentLength);
        }

        $this->addHeader(self::CONTENT_TYPE_HEADER, $this->contentType);

        $this->addHeader(self::CONTENT_TYPE_HEADER, "charset={$this->charset}");
    }

    /**
     * @param OutputContract $output
     * @return void
     */
    private function sendHeaders(OutputContract $output)
    {
        if ($output->isHeadersSent()) {
            return;
        }

        foreach ($this->cookies as $cookie) {
            $output->sendCookie($cookie);
        }

        foreach ($this->headers as $name => $value) {
            $output->sendHeader($name . ": " . implode("; ", $value));
        }
    }

    /**
     * @param OutputContract $output
     * @return void
     */
    private function sendStatusCode(OutputContract $output)
    {
        $output->sendHeader($this->getStatusText($this->statusCode));
    }

    /**
     * @param $statusCode
     * @return string
     */
    protected function getStatusText($statusCode)
    {
        $statusTextMap = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Time-out',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Large',
            415 => 'Unsupported Media Type',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Time-out',
            505 => 'HTTP Version not supported'
        ];

        if (!array_key_exists($statusCode, $statusTextMap)) {
            throw new \InvalidArgumentException('Unknown HTTP Status Code "' . htmlentities($statusCode) . '"');
        }

        $text = $statusTextMap[$statusCode];

        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

        return $protocol . ' ' . $statusCode . ' ' . $text;
    }

    /**
     * return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
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
     * @param string $name
     * @return bool
     */
    public function hasHeader($name)
    {
        return array_key_exists($name, $this->headers);
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
     * @param string $name
     * @return string|null
     */
    public function getHeader($name)
    {
        return $this->hasHeader($name)
            ? implode('; ', $this->headers[$name])
            : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasCookie($name)
    {
        return array_key_exists($name, $this->cookies);
    }

    /**
     * @param CookieContract $cookie
     * @return void
     */
    public function setCookie(CookieContract $cookie)
    {
        $this->cookies[$cookie->getName()] = $cookie;
    }

    /**
     * @param string $name
     * @return CookieContract|null
     */
    public function getCookie($name)
    {
        return $this->hasCookie($name) ? $this->cookies[$name] : null;
    }

    /**
     * @return string|null
     */
    public function getFileName()
    {
        return $this->fileName;
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
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
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
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
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
     * Close resources (if any) used in this Response.
     */
    public function close()
    {
        return;
    }
}
