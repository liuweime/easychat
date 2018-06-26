<?php


class CustomSessionHandle implements SessionHandlerInterface
{
    private $option = [];

    public function __construct(array $option)
    {
        // 检测是否设置了session失效时间
        if (empty($option['maxlifetime'])) {
            $option['maxlifetime'] = ini_get('session.gc_maxlifetime');
        }

        $this->option = $option;
    }

    public function open(string $save_path, string $session_name) : bool
    {

    }

    public function read(string $session_id) : string
    {

    }

    public function write(string $session_id, string $session_data) : bool
    {

    }

    public function destroy(string $session_id) : bool
    {

    }

    public function close() : bool
    {
        // code...
    }

    public function gc(int $maxlifetime) : int
    {
        // code...
    }
}
