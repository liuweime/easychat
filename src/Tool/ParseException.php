<?php


namespace Easychat\Tool;


class ParseException extends \Exception
{
    const CODE = 1000;

    public function __construct($message)
    {
        parent::__construct($message, self::CODE);
    }
}
