<?php
/**
 * @author Roman Gemini <roman_gemini@ukr.net>
 * @date 16.05.16
 * @time 22:19
 */

namespace Kote\Http\Response;


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

    public function write(...$string)
    {
        foreach ($string as $item) {
            $this->content .= $item;
        }
    }

    public function renderContent()
    {
        echo $this->content;
        flush();
    }
}