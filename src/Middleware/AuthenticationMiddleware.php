<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/19
 * Time: 16:02
 */

namespace Easychat\Middleware;


use constant\JWTConst;
use CustomRedis\CustomRedis;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticationMiddleware
{
    protected $alg; //= ["HS256", "HS512", "HS384"];

    protected $ignore;

    public function __construct(array $ignore = [])
    {
        $this->ignore = $ignore;
        $this->alg = JWTConst::ALGLIST;
    }

    protected function fetchToken(ServerRequestInterface $request)
    {
        $headers = $request->getHeader('Authorization');
        $header = isset($headers[0]) ? $headers[0] : '';
        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }

        throw new \Exception('Token not found');
    }

    protected function requestIgnore(ServerRequestInterface $request) : bool
    {
        $uri = "/" . $request->getUri()->getPath();
        $uri = preg_replace("#/+#", "/", $uri);

        foreach ((array)$this->ignore as $ignore) {
            $ignore = rtrim($ignore, "/");
            if (!!preg_match("@^{$ignore}(/.*)?$@", $uri)) {
                return false;
            }
        }

        return true;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $bool = $this->requestIgnore($request);
        if ($bool === false) {
            return $next($request, $response);
        }

        try {
            $now = time();
            $token = $this->fetchToken($request);
            JWT::$leeway = JWTConst::FLUSH_TIME;
            $decode = JWT::decode($token, getenv('JWT_SECRET'), $this->alg);
            $blacklist = app(CustomRedis::class)->hGet(JWTConst::PREFIX.'blacklist', $decode->uid);
            if (!is_null($blacklist) && $decode->iat < $blacklist) {

                throw new ExpiredException('Expired token 1');
            }

            if ($decode->exp < $now && $decode->exp + JWTConst::FLUSH_TIME >= $now) {
                // 记录刷新时间
                app(CustomRedis::class)->hSet(JWTConst::PREFIX . 'blacklist', $decode->uid, $now);

                $decode->iat = $now;
                $decode->exp = $decode->iat + JWTConst::EXPIRATION_TIME;
            }
            $request = $request->withAttribute('token', $decode);

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
