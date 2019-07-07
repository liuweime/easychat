<?php


namespace constant;


class Chat
{
    const CACHE_ROOM_INFO = 'room_info:';
    const CACHE_ROOM_USERS = 'room_users:';
    const CACHE_ROOM_FD = 'room_fd:';

    /**
     * 房间信息缓存时间 1天
     *
     * @var int
     */
    const ROOM_CACHE_TIMEOUT = 86400;
}
