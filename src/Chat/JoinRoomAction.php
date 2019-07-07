<?php


namespace Easychat\Chat;


use App\Services\AuthService;
use constant\Socket;
use Easychat\Tool\Parse;

class JoinRoomAction extends Action
{

    /**
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function run()
    {
        /** @var Chat $chat */
        $chat = Parse::convert($this->frame->data, Chat::class);
        $roomId = $chat->getRoomId();
        $token = $chat->getToken();
        // 获取用户信息
        $user = app(AuthService::class)->user($token);

        // 未在房间中 添加进房间
        $this->room->joinRoom($roomId, $user['uid']);
    }

}
