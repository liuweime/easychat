<?php


namespace Easychat\Auth;


use constant\Auth;
use constant\JWTConst;
use Firebase\JWT\JWT;

trait Token
{

    /**
     * 构建值域
     * iss: issuer 签发人
     * sub: subject 主题
     * aud: audience
     * iat: Issued At 签发时间
     * nbf: Not Before 生效时间
     * exp: expiration time 失效时间
     *
     * @param array $payload
     * @return array
     */
    protected function payload(array $payload) : array
    {
        $now = time();
        $default = [
            'iss' => JWTConst::ISSUER,
            'sub' => JWTConst::SUBJECT,
            'aud' => JWTConst::AUDIENCE,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + JWTConst::EXPIRATION_TIME
        ];

        return empty($payload) ? $default : array_merge($default, $payload);
    }

    /**
     * 加码 生成token
     *
     * @param array $payload
     * @return string
     */
    public function encode(array $payload) : string
    {
        return JWT::encode($this->payload($payload), getenv("JWT_SECRET"));
    }

    /**
     * 更新 token
     *
     * @param string $token
     * @return string
     * @throws AuthException
     */
    public function flush(string $token) : string
    {
        $now = time();
        JWT::$leeway = JWTConst::FLUSH_TIME;
        $user = $this->decode($token);

        // 判断 token 是否超过刷新时间
        if ($user->getMaxFlushTime() < $now && $user->getMaxFlushTime() + JWTConst::FLUSH_TIME > $now) {
            // 记录一下刷新时间 将该时间之前的token记为无效
            $this->storge->hSet(Auth::CACHE_AUTH_TOKEN_BLACKLIST, $user->getUid(), $now);
            $data = $user->toArray();
            $data['iat'] = $now;
            $data['exp'] = $now + JWTConst::EXPIRATION_TIME;

            return $this->encode($data);
        }

        throw new AuthException('token 已失效');
    }

    /**
     * 解码
     *
     * @param string $token
     * @return User
     */
    public function decode(string $token) : User
    {
        return User::convert(JWT::decode($token, getenv('JWT_SECRET'), JWTConst::ALGLIST));
    }
}
