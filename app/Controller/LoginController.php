<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/19
 * Time: 13:42
 */

namespace App\Controller;


use App\Services\EmailService;
use App\Services\TokenService;
use App\Services\UserService;
use Easychat\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginController extends Controller
{
    /** @var UserService  */
    protected $userService;

    /** @var TokenService  */
    protected $tokenService;

    /** @var EmailService  */
    protected $emailService;

    public function __construct(UserService $userService, TokenService $tokenService, EmailService $emailService)
    {
        $this->userService = $userService;
        $this->tokenService = $tokenService;
        $this->emailService = $emailService;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     * @throws \Exception
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $param = $request->getParsedBody();

        try {
            $user = $this->userService->login($param['name'], $param['password']);

            // 生成token
            $token = $this->tokenService->create([
                'uid' => $user['id'],
                'name' => $user['name']
            ]);
        } catch (\Exception $exception) {
            return $this->response($response, [
                'code' => 1,
                'data' => [],
                'message' => $exception->getMessage()
            ]);
        }

        return $this->response($response, [
            'code' => 0,
            'data' => ['token' => $token],
            'message' => '登录成功'
        ]);
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response)
    {
        // 用户注销 jwt 主动销毁
        $bool = $this->userService->logout($request);

        return $this->response($response, [
            'code' => 0,
            'data' => [],
            'message' => $bool === true ? '注销成功' : '注销失败'
        ]);
    }

    public function reset(ServerRequestInterface $request)
    {
        $user = $this->userService->getUserInfo($request);

        $token = $this->tokenService->create([
            'name' => $user->name,
            'uid' => $user->id,
            'type' => 'password_reset'
        ]);
        $address = app(Config::class)->get('app')['address'];
        $link = $address .'/v1/reset_verify/' . $token;
        $subject = '重置密码';
        $email = $user->email;
        $username = $user->name;
        $message = '<p>请点击以下链接重置您的密码。此将于本邮件寄出后2小时后失效。点选此连结后，您将会进入指定网站，并可输入及设定新密码。</p>';
        $message .= '<p><a href="'. $link .'">密码重置</a></p>';

        $result = $this->emailService->sendMessage($subject, $email, $username, $message);
        print_r($result);
    }

    public function resetVerify(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $token = $args['token'];
            $info = $this->tokenService->decode($token);
            if (!$info->type || $info->type !== 'password_reset') {
                throw new \Exception('token invalid');
            }

            // 允许重置
            return $this->response($response, [
                'code' => 0,
                'data' => [],
                'message' => '验证通过'
            ]);
        } catch (\Exception $exception) {
            // 允许重置
            return $this->response($response, [
                'code' => 1,
                'data' => [],
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $data = $request->getParsedBody();
            // 校验 token
            $token = $data['token'];
            $info = $this->tokenService->decode($token);
            if (!$info->type || $info->type !== 'password_reset') {
                throw new \Exception('token invalid');
            }

            $data['uid'] = $info->uid;
            // 重置密码
            $user = $this->userService->reset($data);

            // 生成新 token
            $token = $this->tokenService->create([
                'uid' => $user['id'],
                'name' => $user['name']
            ]);

            return $this->response($response, [
                'code' => 0,
                'data' => ['token' => $token],
                'message' => '登录成功'
            ]);

        } catch (\Exception $exception) {
            return $this->response($response, [
                'code' => $exception->getCode(),
                'data' => [],
                'message' => $exception->getMessage()
            ]);
        }
    }
}
