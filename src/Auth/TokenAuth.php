<?php


namespace Easychat\Auth;

use constant\Auth;
use Easychat\CustomRedis\CustomRedis;
use App\Models\User as UserModel;

class TokenAuth
{
    use Token;

    /** @var CustomRedis */
    private $storge;

    /** @var UserModel  */
    private $model;

    /** @var User */
    protected $user;


    public function __construct()
    {
        $this->storge = app(CustomRedis::class);
        $this->model = app(UserModel::class);
    }

    /**
     * 是否在黑名单中
     *
     * @param string $token
     * @return bool
     */
    public function isBlakced(string $token = '') : bool
    {
        /** @var User $user */
        $user = empty($token) ? $this->user : $this->decode($token);
        if (empty($user)) {

            return true;
        }

        $blackTime = $this->storge->hGet(Auth::CACHE_AUTH_TOKEN_BLACKLIST, $user->getUid());
        if (!is_null($blackTime) && $user->getLastLoginTime() < $blackTime) {
            $this->user = null;

            return true;
        }
        $this->user = $user;

        return false;
    }

    /**
     * 获取用户信息
     *
     * @param string $token
     * @return array
     */
    public function user(string $token) : array
    {
        if (!empty($this->user)) {

            return $this->user->toArray();
        }

        // 黑名单判断
        if ($this->isBlakced($token)) {

            return null;
        }

        return $this->user->toArray();
    }

    /**
     * 判断用户是否登录
     *
     * @param string $token
     * @return bool
     */
    public function check(string $token) : bool
    {
        return !empty($this->user($token));
    }

    /**
     * 用户登录
     *
     * @param string $name
     * @param string $password
     * @return string
     * @throws AuthException
     */
    public function login(string $name, string $password) : string
    {
        // TODO 进行限流 未引入限流器

        $user = $this->model->userFilter(['name' => $name, 'password' => $password])->first();
        if (!empty($user)) {

            return $this->encode(['uid' => $user->id, 'name' => $user->name, 'email' => $user->email]);
        }

        throw new AuthException('昵称或密码错误');
    }

    /**
     * 用户注销
     *
     * @param string $token
     * @return bool
     * @throws AuthException
     */
    public function logout(string $token) : bool
    {
        if ($this->check($token)) {

            $this->storge->hSet(Auth::CACHE_AUTH_TOKEN_BLACKLIST, $this->user->getUid(), time());
            return true;
        }

        throw new AuthException('用户不存在或为登录');
    }

    /**
     * 用户注册
     *
     * @param array $data
     * @return string
     * @throws AuthException
     */
    public function register(array $data) : string
    {
        $filter = ['name' => $data['name']];
        $user = $this->model->userFilter($filter)->first();
        if (!empty($user)) {
            throw new AuthException('用户已存在');
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->model->fill($data);
        $this->model->save();

        return $this->encode(['uid' => $this->model->id, 'name' => $this->model->name, 'email' => $this->model->email]);
    }

    /**
     * 重置密码
     */
    public function reset()
    {

    }
}
