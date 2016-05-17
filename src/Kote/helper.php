<?php

function e($string)
{
    return htmlentities($string, ENT_COMPAT | ENT_HTML401);
}
