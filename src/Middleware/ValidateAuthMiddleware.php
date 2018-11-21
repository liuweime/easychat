<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/19
 * Time: 14:06
 */
namespace Easychat\Middleware;

use constant\Auth;
use Overtrue\Validation\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ValidateAuthMiddleware
{
    protected $ruleTpl = [
        Auth::LOGIN => [
            'name' => 'required',
            'password' => 'required',
        ],
        Auth::REGISTER => [
            'name' => 'required|min:|max:20|unique:User',
            'password' => 'required|min:6',
            'email' => 'required|email|unique:User'
        ],
        Auth::RESET => [
            'token' => 'required',
            'password' => 'required|min:6'
        ]
    ];

    protected $rules = [];

    public function __construct($type = Auth::LOGIN)
    {
        if (empty($this->ruleTpl[$type])) {
            throw new \Exception('auth middleware type error');
        }

        $this->rules = $this->ruleTpl[$type];
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $next
     * @return mixed
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $validator = app(Factory::class)->make($request->getParsedBody(), $this->rules);
        if ($validator->fails()) {
            return $response->withStatus(200)
                ->withHeader('Content-type', 'application/json')
                ->write(json_encode([
                    'code' => 0,
                    'data' => [
                        'failure_results' => $validator->messages()->all()
                    ],
                    'msg' => ''
                ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }

        return $next($request, $response);
    }

}
