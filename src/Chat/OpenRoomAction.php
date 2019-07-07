<?php


namespace Easychat\Chat;
use App\Models\ChatRoom;
use App\Services\AuthService;
use App\Services\TokenService;
use constant\Chat as ChatConst;
use constant\Socket;
use Easychat\Tool\Parse;


class OpenRoomAction extends Action
{

    /**
     * @return bool
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \ReflectionException
     */
    public function run()
    {
        /** @var Chat $chat */
        $chat = Parse::convert($this->frame->data, Chat::class);
        if (empty($chat->getRoomId())) {
            throw new \Exception('未知的房间');
        }
        $roomId = $chat->getRoomId();

        // 获取用户信息
        $token = $chat->getToken();
        $user = app(AuthService::class)->user($token);

        // 获取房间信息
        $room = $this->room->roomInfo($roomId);
        if (empty($room)) {
            $this->server->push($this->frame->fd, json_encode([
                'type' => Socket::TYPE_NO_ROOM,
                'msg' => '房间不存在，或已经被关闭'
            ]));
            return false;
        }

        // 查询是否在房间中
        $bool = $this->room->isRoomUser($room['id'], $user['uid']);
        if (!$bool) {
            // 不在房间中
            $this->server->push($this->frame->fd, json_encode([
                'type' => Socket::TYPE_ILLEGAL_OPEN,
                'msg' => '你未加入该房间，或你已被移出了房间'
            ]));
        }

        // 用户已登录 房间存在 且是该房间用户
        // 获取房间中其他用户的fd信息
        $userFdList = $this->getFdList($room['id']);
        if (!empty($userFdList)) {
            // 进行广播
            $message = json_encode([
                'type' => Socket::TYPE_WELCOME,
                'msg' => sprintf("用户 %s 进入本房间", $user['name']),
                'datetime' => date('Y-m-d H:i:s')
            ]);
            foreach ($userFdList as $fd) {
                $this->server->push($fd, $message);
            }
        }
    }

    /**
     * @param int $roomId
     * @return mixed
     */
    private function getFdList(int $roomId)
    {
        return $this->storge->sEmebers(ChatConst::CACHE_ROOM_FD . $roomId);
    }

}
