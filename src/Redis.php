<?php

namespace src;

use Predis\Client;

class Redis
{

    public static function connect()
    {
        $redisConfig = config('redis');
        if (empty($redisConfig)) {
            throw new \Exception("The Redis Config Not Found", 1);
        }

        return new Client($redisConfig);
    }
}
