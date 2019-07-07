<?php


namespace Easychat\Chat;


use App\Services\AuthService;
use constant\Socket;
use Easychat\Tool\Parse;

class LoginAction extends Action
{

    public function run()
    {
        try {
            /** @var Chat $chat */
            $chat = Parse::convert($this->frame->data, Chat::class);
            $data = $chat->getData();
            if (!isset($data['name']) || !isset($data['password'])) {
                throw new \Exception('账号名称或密码未传递');
            }

            // 登录
            $token = app(AuthService::class)->login($data['name'], $data['password']);
            $this->server->push($this->frame->fd, json_encode([
                'type' => Socket::TYPE_LOGIN,
                'msg' => '登录成功',
                'token' => $token
            ]));
        } catch (\Exception $exception) {
            $this->server->push($this->frame->fd, json_encode([
                'type' => Socket::TYPE_ERROR,
                'msg' => $exception->getMessage(),
            ]));
        }
    }
}
