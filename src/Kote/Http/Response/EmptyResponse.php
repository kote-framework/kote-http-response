<?php
/**
 * @author Roman Gemini <roman_gemini@ukr.net>
 * @date 16.05.16
 * @time 22:13
 */

namespace Kote\Http\Response;


class EmptyResponse extends Response
{
    public function render()
    {
        // Don't render anything
    }

    protected function renderContent()
    {
        // Don't render anything
    }
}