<?php


namespace Easychat\Chat;


use App\Models\User;
use App\Services\AuthService;
use App\Services\TokenService;
use App\Services\UserService;
use constant\Socket;
use Firebase\JWT\ExpiredException;

class Chat
{
    /** @var AuthService  */
    private $auth;

    /** @var TokenService  */
    private $token;

    /** @var UserService  */
    private $user;

    private $userinfo;

    public function __construct(AuthService $auth, TokenService $token, UserService $user)
    {
        $this->auth = $auth;
        $this->token = $token;
        $this->user = $user;
    }


}
