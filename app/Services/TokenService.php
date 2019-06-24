<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/19
 * Time: 13:44
 */

namespace App\Services;


use constant\JWTConst;
use Easychat\CustomRedis\CustomRedis;
use Firebase\JWT\JWT;

class TokenService extends Service
{


    /**
     * @param array $userPayload
     * @return array
     */
    protected function buildPayload(array $userPayload = [])
    {
        $now = time();
        $payload = [
            'iss' => JWTConst::ISSUER,
            'sub' => JWTConst::SUBJECT,
            'aud' => JWTConst::AUDIENCE,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + JWTConst::EXPIRATION_TIME
        ];
        if (!empty($userPayload)) {
            $payload = array_merge($userPayload, $payload);
        }
        return $payload;
    }

    /**
     * @param array $payload
     * @return string
     */
    public function create(array $payload = [])
    {
        return JWT::encode($this->buildPayload($payload), getenv("JWT_SECRET"));
    }

    public function update(array $payload)
    {
        return JWT::encode($payload, getenv("JWT_SECRET"));
    }

    public function decode(string $token)
    {
        return JWT::decode($token, getenv('JWT_SECRET'), JWTConst::ALGLIST);
    }


}
