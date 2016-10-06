<?php

namespace Nerd\Framework\Http\Response;

class PlainResponse extends Response
{
    private $content = "";

    public function __construct($content = "") {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    public function write($string)
    {
        $this->content .= $string;
    }

    public function renderContent()
    {
        echo $this->content;
        flush();
    }
}
