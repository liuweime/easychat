<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/19
 * Time: 13:44
 */

namespace App\Services;


use App\Models\User;
use constant\JWTConst;
use CustomRedis\CustomRedis;
use Illuminate\Database\Capsule\Manager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserService extends Service
{
    /** @var User  */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param ServerRequestInterface $request
     * @return array|bool
     */
    public function login(ServerRequestInterface $request)
    {
        $user = $this->user->where('name', $request->getParsedBody()['name'])->first();
        if (password_verify($request->getParsedBody()['password'], $user->password) === false) {
            return false;
        }

        return $user->toArray();
    }

    public function logout(ServerRequestInterface $request)
    {
        $user = $request->getAttribute('token');
        $now = time();

        app(CustomRedis::class)->hSet(JWTConst::PREFIX.'blacklist', $user->uid, $now);

        return true;
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function register(ServerRequestInterface $request)
    {
        $data = $request->getParsedBody();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $this->user->fill($data);
        $this->user->save();

        return true;
    }

    public function getUserInfo(ServerRequestInterface $request)
    {
        $user = $request->getAttribute('token');
        $uid = $user->uid;

        return $this->user->where('id', $uid)->first();
    }

    public function reset(array $data)
    {
        $user = $this->user->where('id', $data['uid'])->first();
        if (empty($user)) {
            throw new \Exception('User not found');
        }

        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->save();

        app(CustomRedis::class)->hset(JWTConst::PREFIX.'blacklist', $user->id, time());

        return $user->toArray();
    }
}
