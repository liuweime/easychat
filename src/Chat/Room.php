<?php


namespace Easychat\Chat;


use App\Models\ChatRoom;
use constant\Chat as ChatConst;
use Easychat\CustomRedis\CustomRedis;

class Room
{
    /** @var CustomRedis */
    private $storge;

    /** @var ChatRoom */
    private $chatRoom;

    public function __construct()
    {
        $this->storge = app(CustomRedis::class);
        $this->storge = new ChatRoom();
    }

    /**
     * 返回房间信息
     *
     * @param int $roomId
     * @return array
     */
    public function roomInfo(int $roomId) : array
    {
        // 首先从缓存中获取
        $res = $this->storge->get(ChatConst::CACHE_ROOM_INFO . $roomId);
        if (!empty($res)) {

            return unserialize($res);
        }

        // 从数据库中获取
        $roomInfo = $this->chatRoom->where('id', $roomId)->first()->toArray();
        // 重新写入库中 一天+随机0~2小时的缓存时间
        $ttl = ChatConst::ROOM_CACHE_TIMEOUT + rand(0, 7200);
        $this->storge->set(ChatConst::CACHE_ROOM_INFO . $roomId, serialize($roomInfo), $ttl);

        return $roomInfo;
    }

    /**
     * 是否是本房间用户
     *
     * @param int $roomId
     * @param int $uid
     * @return bool
     */
    public function isRoomUser(int $roomId, int $uid) : bool
    {
        $res = $this->storge->hExists(ChatConst::CACHE_ROOM_USERS . $roomId, $uid);

        return (bool)$res;
    }

    public function joinRoom(int $roomId, int $uid) : bool
    {
        $roomInfo = $this->roomInfo($roomId);
        if (empty($roomInfo)) {
            throw new \Exception('room not found');
        }

        $bool = $this->isRoomUser($roomId, $uid);
        if ($bool) {
            throw new \Exception('joined room');
        }

        // 添加
//        $this->chatRoom::created()
    }
}
