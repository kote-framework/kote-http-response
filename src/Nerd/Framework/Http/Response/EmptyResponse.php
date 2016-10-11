<?php

namespace Nerd\Framework\Http\Response;

use Nerd\Framework\Http\OutputContract;

class EmptyResponse extends Response
{
    protected function renderContent(OutputContract $output)
    {
        // Do not render anything
    }
}
