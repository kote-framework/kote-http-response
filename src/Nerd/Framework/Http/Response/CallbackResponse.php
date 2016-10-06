<?php

namespace Nerd\Framework\Http\Response;

class CallbackResponse extends Response
{
    private $callable;

    public function __construct(callable $callable, $statusCode = StatusCode::HTTP_OK)
    {
        $this->setStatusCode($statusCode);
        $this->setCallable($callable);
    }

    private function setCallable(callable $callable)
    {
        $this->callable = $callable;
    }

    protected function renderContent()
    {
        $writer = function ($content) {
            echo $content;
        };
        call_user_func($this->callable, $writer);
    }
}
