<?php
/**
 * @author Roman Gemini <roman_gemini@ukr.net>
 * @date 16.05.16
 * @time 22:13
 */

namespace Nerd\Framework\Http\Response;


class EmptyResponse extends Response
{
    protected function renderContent()
    {
        // Do not render anything
    }
}