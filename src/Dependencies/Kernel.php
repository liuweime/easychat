<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/17
 * Time: 17:09
 */

namespace Easychat\Dependencies;

use App\Models\User;
use Easychat\CustomRedis\CustomRedis;
use Easychat\Config;
use Easychat\Validation\DatabasePresenceVerifier;
use Illuminate\Database\Capsule\Manager;
use Overtrue\Validation\Factory;
use Slim\App;
use Illuminate\Database\Capsule\Manager as EloquentManager;
use Overtrue\Validation\Translator;
use Overtrue\Validation\Factory as ValidatorFactory;
use Slim\Collection;
use Slim\Container;

class Kernel
{
    /** @var Container  */
    protected static $container;

    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
        self::$container = $this->app->getContainer();
    }

    public function loadDependencies()
    {
        $capsule = new EloquentManager;
        $capsule->addConnection(self::$container['settings']['db']);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        // Eloquent
        self::$container[Manager::class] = function ($container) use ($capsule) {
            return $capsule;
        };

        // validator
        self::$container[Factory::class] = function ($container) {
            $validator = new ValidatorFactory(new Translator());
            $validator->setPresenceVerifier(new DatabasePresenceVerifier($container[Manager::class]->getDatabaseManager()));
            return $validator;
        };

        self::$container[CustomRedis::class] = function ($container) {
            return new CustomRedis($container['settings']['redis']);
        };

        self::$container[Config::class] = function ($container) {
            return new Config();
        };
    }

    public function loadProvider()
    {
        $providers = self::$container['settings']['providers'];
        if (!empty($providers) && is_array($providers)) {
            foreach ($providers as $provider) {
                self::$container->register(new $provider);
            }
        }
    }

    public function loadMiddleware()
    {
        $middlewares = self::$container['settings']['middleware'];
        if (!empty($middlewares) && is_array($middlewares)) {
            foreach ($middlewares as $middleware) {
                $this->app->add($middleware);
            }
        }
    }

    public function run()
    {
        $this->loadDependencies();
        $this->loadProvider();
        $this->loadMiddleware();


        $this->app->run();
    }

    public static function getContainer()
    {
        return self::$container;
    }
}
