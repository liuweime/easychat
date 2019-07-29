<?php


namespace Easychat\Auth;


class AuthException extends \Exception
{
    const CODE = 2000;

    public function __construct($message)
    {
        parent::__construct($message, self::CODE);
    }
}
