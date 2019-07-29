<?php


namespace Easychat\Auth;


class User
{
    /** @var int 用户ID */
    private $uid;

    /** @var string 用户昵称 */
    private $name;

    /** @var string 用户邮箱 */
    private $email;

    /** @var string 上次更新时间 */
    private $lastLoginTime;

    /** @var int 最大刷新时间 */
    private $max_flush_time;


//    /** @var string 上次登录ip */
//    private $ip;
//
//    /** @var string 上次登录地点 */
//    private $addr;

    public function toArray()
    {
        return [
            'uid' => $this->getUid(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'last_login_time' => $this->getLastLoginTime()
        ];
    }

    /**
     * @param object $obj
     * @return User
     */
    public static function convert(object $obj)
    {
        $user = new User();
        $user->setUid($obj->uid);
        $user->setName($obj->name);
        $user->setEmail($obj->email);
        $user->setLastLoginTime($obj->iat);
        $user->setMaxFlushTime($obj->exp);

        return $user;
    }

    /**
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getLastLoginTime(): string
    {
        return $this->lastLoginTime;
    }

    /**
     * @param string $lastLoginTime
     */
    public function setLastLoginTime(string $lastLoginTime): void
    {
        $this->lastLoginTime = $lastLoginTime;
    }

    /**
     * @return int
     */
    public function getMaxFlushTime(): int
    {
        return $this->max_flush_time;
    }

    /**
     * @param int $max_flush_time
     */
    public function setMaxFlushTime(int $max_flush_time): void
    {
        $this->max_flush_time = $max_flush_time;
    }


}
