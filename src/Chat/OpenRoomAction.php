<?php


namespace Easychat\Chat;
use App\Models\ChatRoom;
use App\Services\AuthService;
use App\Services\TokenService;
use constant\Chat as ChatConst;
use constant\Socket;


class OpenRoomAction extends Action
{

    /**
     * @return bool
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function run()
    {
        $data = json_encode($this->frame->data, true);
        if (!isset($data['room_id'])) {
            throw new \Exception('未知的房间');
        }

        // 获取用户信息
        $token = $data['token'];
        $user = app(AuthService::class)->user($token);


        // 获取房间信息
        $room = $this->getRoomInfo($data['room_id']);
        if (empty($room)) {
            $this->server->push($this->frame->fd, json_encode([
                'type' => Socket::TYPE_NO_ROOM,
                'msg' => '房间不存在，或已经被关闭'
            ]));
            return false;
        }

        // 查询是否在房间中
        $bool = $this->isRoomUser($room['id'], $user['uid']);
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

    /**
     * 是否是本房间用户
     * @param int $roomId
     * @param int $uid
     * @return bool
     */
    private function isRoomUser(int $roomId, int $uid) : bool
    {
        $res = $this->storge->hExists(ChatConst::CACHE_ROOM_USERS . $roomId, $uid);

        return (bool)$res;
    }

    /**
     * @param int $roomId
     * @return mixed
     */
    private function getRoomInfo(int $roomId)
    {
        // 首先从缓存中获取
        $res = $this->storge->get(ChatConst::CACHE_ROOM_INFO . $roomId);
        if (empty($res)) {
            // 从数据库中获取
            $roomModel = new ChatRoom();
            $roomInfo = $roomModel->where('id', $roomId)->first()->toArray();
            $this->storge->set(ChatConst::CACHE_ROOM_INFO . $roomId, serialize($roomInfo), ChatConst::ROOM_CACHE_TIMEOUT);
        } else {
            $roomInfo = unserialize($res);
        }

        return $roomInfo;
    }
}
