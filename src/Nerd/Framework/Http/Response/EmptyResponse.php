<?php
/**
 * @author Roman Gemini <roman_gemini@ukr.net>
 * @date 16.05.16
 * @time 22:13
 */

namespace Nerd\Framework\Http\Response;


use Nerd\Framework\Http\OutputContract;

class EmptyResponse extends Response
{
    protected function renderContent(OutputContract $output)
    {
        // Do not render anything
    }
}