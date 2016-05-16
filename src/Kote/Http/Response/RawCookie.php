<?php
/**
 * @author Roman Gemini <roman_gemini@ukr.net>
 * @date 16.05.16
 * @time 22:12
 */

namespace Kote\Http\Response;


class RawCookie extends Cookie
{
    /**
     * Sends raw cookie to client.
     */
    public function send()
    {
        setrawcookie(
            $this->getName(),
            $this->getValue(),
            $this->getExpire(),
            $this->getPath(),
            $this->getDomain(),
            $this->getSecure(),
            $this->getHttp()
        );
    }
}