<?php


namespace Easychat\Chat;


use App\Services\AuthService;
use constant\Socket;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class Message
{
    private $frame;

    private $server;

    private $type;

    public function __construct(Server $server, Frame $frame)
    {
        $this->frame = $frame;
        $this->server = $server;
    }


    public function action()
    {
        $data = !empty($this->frame->data) ? json_decode($this->frame->data, true) : [];
        if (empty($data)) {
            throw new \Exception('Error: not found frame data');
        }
        $this->type = $data['type'];

        switch ($this->type) {
            case Socket::TYPE_LOGIN:
                $action = new LoginAction($this->server, $this->frame);
                break;
            case Socket::TYPE_LOGOUT:
                break;
            case Socket::TYPE_CHANGE_ROOM:
                break;
            case Socket::TYPE_JOIN_ROOM:

                break;
            case Socket::TYPE_OPEN_ROOM:
                $action = new OpenRoomAction($this->server, $this->frame);
                break;
            default:
                $action = new NoAction($this->server, $this->frame);
                break;
        }
        $action->run();
    }
}
