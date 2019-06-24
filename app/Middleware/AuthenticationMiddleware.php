<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/19
 * Time: 16:02
 */

namespace App\Middleware;


use App\Services\AuthService;
use App\Services\TokenService;
use Firebase\JWT\ExpiredException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticationMiddleware
{
    protected $ignore;

    public function __construct(array $ignore = [])
    {
        $this->ignore = $ignore;
    }

    /**
     * @param ServerRequestInterface $request
     * @return mixed
     * @throws \Exception
     */
    protected function fetchToken(ServerRequestInterface $request)
    {
        $headers = $request->getHeader('Authorization');
        $header = isset($headers[0]) ? $headers[0] : '';
        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }

        throw new \Exception('Token not found');
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    protected function requestIgnore(ServerRequestInterface $request) : bool
    {
        $uri = "/" . $request->getUri()->getPath();
        $uri = preg_replace("#/+#", "/", $uri);
        foreach ((array)$this->ignore as $ignore) {
            $ignore = rtrim($ignore, "/");
            if (!!preg_match("@^{$ignore}(/.*)?$@", $uri)) {
                return true;
            }
        }

        return false;
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
        $bool = $this->requestIgnore($request);
        if ($bool === false) {
            return $next($request, $response);
        }

        try {
            // 获取token
            $token = $this->fetchToken($request);

            $user = app(TokenService::class)->decode($token);
            // 检测是否为合法的token
            $bool = app(AuthService::class)->isBlackedToken($user);
            if ($bool) {

                throw new ExpiredException('Expired token 1');
            }

            // 解析用户信息
            $token = app(AuthService::class)->flush($user);
            if (!empty($token)) {
                // 写入
                $response = $response->withHeader('Authorization', 'Bearer ' . $token);
            }
            $request = $request->withAttribute('token', $user);

        } catch (\Exception $exception) {
            return $response->withStatus(200)
                ->withHeader('Content-type', 'application/json')
                ->write(json_encode([
                    'code' => $exception->getCode(),
                    'data' => [],
                    'msg' => $exception->getMessage()
                ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }

        return $next($request, $response);
    }
}
