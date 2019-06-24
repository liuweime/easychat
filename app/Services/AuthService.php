<?php


namespace App\Services;


use constant\Auth;
use constant\JWTConst;
use Easychat\CustomRedis\CustomRedis;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class AuthService extends Service
{

    /** @var CustomRedis  */
    protected $redis;

    protected $token;

    public function __construct(TokenService $service)
    {
        $this->redis = app(CustomRedis::class);
        $this->token = $service;
    }


    /**
     * @param $user
     * @return bool
     */
    public function isBlackedToken($user) : bool
    {
        if (is_string($user)) {
            $user = $this->token->decode($user);
        }

        // 获取用户黑名单记录时间
        $blackTime = $this->redis->hGet(Auth::CACHE_AUTH_TOKEN_BLACKLIST, $user->uid);
        if (!is_null($blackTime) && $user->iat < $blackTime) {

            return true;
        }

        return false;
    }

    /**
     * @param $user
     * @return string
     */
    public function flush($user)
    {
        $now = time();
        JWT::$leeway = JWTConst::FLUSH_TIME;

        if (is_string($user)) {
            $user = $this->token->decode($user);
        }
        $expireTime = $user->exp;

        if ($expireTime < $now && $expireTime + JWTConst::FLUSH_TIME > $now) {
            // 记录一下刷新时间 将该时间之前的token记为无效
            $this->redis->hSet(Auth::CACHE_AUTH_TOKEN_BLACKLIST, $user->uid, $now);

            // 获取新的token
            $user->iat = $now;
            $user->exp = $user->iat + JWTConst::EXPIRATION_TIME;

            return $this->token->update((array)$user);
        }

        return '';
    }

    public function user(string $token)
    {
        $user = $this->token->decode($token);
        $bool = $this->isBlackedToken($user);
        if ($bool) {
            throw new ExpiredException('Expired token 1');
        }
        $user = $this->flush($user);
        $uid = $user['uid'];

    }
}
