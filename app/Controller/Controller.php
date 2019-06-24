<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/19
 * Time: 11:17
 */

namespace App\Controller;


use Psr\Http\Message\ResponseInterface;

class Controller
{
    public function response(ResponseInterface $response, array $res)
    {
        return $response->withStatus(200)
            ->withHeader('Content-type', 'application/json')
            ->write(json_encode($res, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
}
