<?php

namespace Nerd\Framework\Http\Response;

use Nerd\Framework\Http\IO\OutputContract;

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

    protected function renderContent(OutputContract $output)
    {
        call_user_func($this->callable, $output);
    }
}
