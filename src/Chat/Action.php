<?php


namespace Easychat\Chat;


use Easychat\CustomRedis\CustomRedis;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

abstract class Action
{
    /** @var Server */
    protected $server;

    /** @var Frame */
    protected $frame;

    /** @var CustomRedis */
    protected $storge;

    /** @var Room */
    protected $room;

    public function __construct(Server $server, Frame $frame)
    {
        $this->server = $server;
        $this->frame = $frame;
        $this->storge = app(CustomRedis::class);
        $this->room = app(Room::class);
    }

    abstract public function run();
}
