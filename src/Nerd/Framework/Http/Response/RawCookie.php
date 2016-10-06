<?php

namespace Nerd\Framework\Http\Response;

class RawCookie extends Cookie
{
    public function isRaw()
    {
        return true;
    }
}
