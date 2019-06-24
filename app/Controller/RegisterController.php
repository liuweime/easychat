<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/19
 * Time: 15:00
 */

namespace App\Controller;


use App\Services\TokenService;
use App\Services\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RegisterController extends Controller
{
    protected $userService;

    protected $tokenService;

    public function __construct(UserService $userService, TokenService $tokenService)
    {
        $this->userService = $userService;
        $this->tokenService = $tokenService;
    }

    public function test()
    {
        return 'hello';
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     */
    public function store(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            // 注册
            $this->userService->register($request);
            // 登录 生成token
            $token = $this->tokenService->create();

            return $this->response($response, [
                'code' => 0,
                'data' => ['token' => $token],
                'message' => '登录成功'
            ]);
        } catch (\Exception $exception) {
            return $this->response($response, [
                'code' => $response->getStatusCode(),
                'data' => [],
                'message' => $exception->getMessage()
            ]);
        }
    }
}
