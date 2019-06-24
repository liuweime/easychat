<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/20
 * Time: 13:33
 */

namespace App\Services;


use Easychat\Config;

class EmailService extends Service
{
    protected $transport;

    protected $mailer;

    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge($config, app(Config::class)->get('email'));

        $this->transport = new \Swift_SmtpTransport($this->config['host'], $this->config['port'], 'ssl');
        $this->transport->setUsername($this->config['user'])->setPassword($this->config['password']);

        $this->mailer = new \Swift_Mailer($this->transport);
    }
    
    public function sendMessage(string $subject, string $address, string $name, string $message)
    {
        $handler = new \Swift_Message($subject);
        $handler->setFrom($this->config['user'], $this->config['user'])
            ->setTo($address, $name)
            ->setBody($message, 'text/html');

        return $this->mailer->send($handler);
    }
}
