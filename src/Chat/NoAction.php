<?php


namespace Easychat\Chat;


use constant\Socket;

class NoAction extends Action
{

    public function run()
    {
        $this->server->push($this->frame->fd, json_encode([
            'type' => Socket::TYPE_NO_ACTION,
            'msg' => '错误的请求',
        ]));
    }

}
