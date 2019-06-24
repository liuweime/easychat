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

    const TYPE_NO_LOGIN = -2;
    const TYPE_NO_MESSAGE = -3;
    const TYPE_ERROR = -1;
}
