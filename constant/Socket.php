<?php

namespace constant;

class Socket
{
    const TYPE_LOGIN = 0;
    const TYPE_LOGOUT = 1;
    const TYPE_WELCOME  = 2;
    const TYPE_MESSAGE = 3;
    const TYPE_REFRESH_TOKEN = 4;
    const TYPE_CHANGE_ROOM = 5;
    const TYPE_JOIN_ROOM = 6;
    const TYPE_OPEN_ROOM = 7;

    const TYPE_NO_LOGIN = -2;
    const TYPE_NO_MESSAGE = -3;
    const TYPE_ERROR = -1;
    const TYPE_NO_ACTION = 'NoAction';
    const TYPE_NO_ROOM = 'WrongRoom';
    const TYPE_ILLEGAL_OPEN = 'IllegalOpenRoom';
    const TYPE_JOINED = 'JoinedRoom';
}
