<?php

namespace Nerd\Framework\Http\IO;

use Nerd\Framework\Http\CookieContract;
use Nerd\Framework\Http\OutputContract;

class GenericHttpOutput implements OutputContract
{
    public function sendCookie(CookieContract $cookie)
    {
        $setter = $cookie->isRaw() ? 'setrawcookie' : 'setcookie';

        $setter(
            $cookie->getName(),
            $cookie->getValue(),
            $cookie->getExpire(),
            $cookie->getPath(),
            $cookie->getDomain(),
            $cookie->isSecure(),
            $cookie->isHttpOnly()
        );
    }

    public function sendHeader($header)
    {
        header($header);
    }

    public function sendData($data)
    {
        echo $data;
        flush();
    }

    public function isHeadersSent()
    {
        return headers_sent();
    }
}
