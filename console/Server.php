<?php

namespace Easychat;

use App\Models\ChatRoom;
use App\Models\User;
use App\Services\AuthService;
use App\Services\TokenService;
use constant\JWTConst;
use constant\Socket;
use Easychat\Chat\Chat;
use Easychat\CustomRedis\CustomRedis;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class WebSocketServer
{

    /** @var Server  */
    private $master;

    /** @var mixed  */
    private $storage;

    protected $config;

    protected $user;

    /** @var Chat */
    protected $chat;

    /** @var AuthService  */
    private $auth;

    /** @var TokenService  */
    private $token;

    protected $roomId;

    /**
     * WebSocketServer constructor.
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct()
    {
        $this->config = app(Config::class)['socket'];

        $this->master = new Server($this->config['host'], $this->config['port']);
        // 进行某些设置
        $this->master->set($this->config);

        $this->storage = app(CustomRedis::class);

        $this->chat = app(Chat::class);
        $this->auth = app(AuthService::class);
        $this->token = app(TokenService::class);
    }

    public function run()
    {
        $this->master->on('open', [$this, 'open']);
        $this->master->on('message', [$this, 'message']);
        $this->master->on('close', [$this, 'close']);

        $this->master->start();
    }

    /**
     * @param Server $server
     * @param Frame $frame
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function message(Server $server, Frame $frame)
    {
        try {
            $message = [];
            $data = !empty($frame->data) ? json_decode($frame->data, true) : [];
            if (empty($data)) {
                throw new \Exception('Error: not found frame data');
            }

            if ($data['type'] === Socket::TYPE_CHANGE_ROOM || $data['type'] === Socket::TYPE_LOGIN) {
                if (!isset($data['room_id'])) {
                    throw new \Exception('Error: unspecified room id');
                }
                $this->roomId = $data['room_id'];
            } else {
                if (empty($this->roomId)) {
                    throw new \Exception('Error: unspecified room id');
                }
            }

            if (empty($this->user)) {
                throw new \Exception('Error: user need login');
            }

            // 如果该房间是私聊 查询用户是否在该房间
            $roomId = $this->roomId;
            $roomInfo = app(ChatRoom::class)->where('id', $roomId)->first();
            if (empty($roomInfo)) {
                throw new \Exception('Error: room not exists');
            }

            // 获取聊天室用户
            $users = $roomInfo->users;
            $uids = array_map(function ($user) {
                return $user->id;
            }, $users);

            // 非公共聊天室 判断用户是否加入房间
            if ($roomInfo->type !== 0 && !in_array($this->user->id, $uids)) {
                // 未加入房间
                throw new \Exception('You have not joined the chat room yet ');
            }

            // 获取用户在聊天室的状态
            $statusKey = $this->config['prefix'] . 'chat_status:' .$roomInfo->id;
            if (false !== $this->storage->hExists($statusKey, $this->user->id)) {
                $status = $this->storage->hGet($statusKey, $this->user->id);
            } else {
                $status = 0;
            }

            // 更新当前用户连接ID
            $this->storage->hSet($this->config['prefix'] . 'chat_fd:' . $roomInfo->id, $this->user->id, $frame->fd);

            if ($data['type'] === Socket::TYPE_LOGIN) {
                $message['type'] = Socket::TYPE_WELCOME;
            } elseif ($data['type'] === Socket::TYPE_LOGOUT) {
                $message['type'] = Socket::TYPE_LOGOUT;
            } else {
                $message['type'] = Socket::TYPE_MESSAGE;
            }

            $message['content'] = empty($data['content'])?'':$data['content'];
            $message['name'] = $this->user->name;
            $message['uid'] = $this->user->id;
            $message['status'] = $status;

            $this->send($this->roomId, $message);

        } catch (\Exception $exception) {
            $message['type'] = Socket::TYPE_ERROR;
            $message['message'] = $exception->getMessage();
            $this->master->push($frame->fd, json_encode($message));
        }
    }

    /**
     * @param Server $server
     * @param Request $request
     * @return bool
     */
    public function open(Server $server, Request $request)
    {
        $token = $request->header['token'];
        if (empty($token)) {
            $server->push($request->fd, json_encode([
                'type' =>  Socket::TYPE_ERROR,
                'msg' => 'token not found'
            ]));
            return false;
        }

        // 判断是否是黑名单中
        $bool = $this->auth->isBlackedToken($token);
        if ($bool) {
            $server->push($request->fd, json_encode([
                'type' =>  Socket::TYPE_NO_LOGIN,
                'msg' => 'expire token'
            ]));
            return false;
        }

        // 刷新token
        if ($info = $this->auth->flush($token)) {
            $message['type'] = Socket::TYPE_REFRESH_TOKEN;
            $message['token'] = $info;

            $server->push($request->fd, json_encode([
                'type' =>  Socket::TYPE_REFRESH_TOKEN,
                'msg' => '',
                'token' => $info
            ]));
        }

        $server->push($request->fd, json_encode([
            'type' => Socket::TYPE_WELCOME,
            'msg' => '欢迎'
        ]));
    }

    /**
     * @param Server $server
     * @param $fd
     */
    public function close(Server $server, $fd)
    {
        echo 'close ' ;//, $frame->id;
    }

    /**
     * @param int $roomId
     * @param array $message
     * @param int $clientId
     */
    protected function send(int $roomId, array $message, int $clientId = 0)
    {
        // 获取房间中所有用户
        $fds = $this->storage->hVals($this->config['prefix'] . 'chat_fd:'. $roomId);
        if (!empty($fds)) {
            foreach ($fds as $fd) {
                $this->master->push($fd, json_encode($message));
            }
        }
    }
}
