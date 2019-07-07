<?php


namespace App\Services;


use App\Models\User;
use constant\Auth;
use constant\JWTConst;
use Easychat\CustomRedis\CustomRedis;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use mysql_xdevapi\Exception;

class AuthService extends Service
{

    /** @var CustomRedis  */
    protected $redis;


    private $user;

    protected $token;

    public function __construct(TokenService $token, UserService $user)
    {
        $this->redis = app(CustomRedis::class);
        $this->token = $token;
        $this->user = $user;
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

    /**
     * 获取token用户信息
     *
     * @param string $token
     * @return array
     * @throws \Exception
     */
    public function user(string $token) : array
    {
        $user = $this->token->decode($token);
        if ($this->isBlackedToken($user)) {
            throw new \Exception('token expired');
        }

        return $user;
    }

    /**
     * 用户登录
     * @param array $data
     * @return string
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function login($name, $password) : string
    {
        $user = app(User::class)->where('name', $name)->first();
        if (!password_verify($password, $user->password)) {

            throw new \Exception('账号或密码不正确');
        }

        // 生成token
        return $this->token->create(['uid' => $user->id, 'name' => $user->name]);
    }
}
