<?php
/**
 * @author Roman Gemini <roman_gemini@ukr.net>
 * @date 16.05.16
 * @time 22:18
 */

namespace Nerd\Framework\Http\Response;


class JsonResponse extends Response
{
    const JSON_CONTENT_TYPE = "application/json";

    private $data;

    public function __construct($data = null, $statusCode = StatusCode::HTTP_OK)
    {
        $this->setContentType(self::JSON_CONTENT_TYPE);
        $this->setStatusCode($statusCode);
        $this->setData($data);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function renderContent()
    {
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE);
    }
}