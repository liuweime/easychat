<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/17
 * Time: 16:24
 */

return [
    'httpVersion' => getenv('APP_HTTP_VERSION'),
    'responseChunkSize' => getenv('APP_RESPONSE_CHUNK_SIZE'),
    'outputBuffering' => getenv('APP_OUTPUT_BUFFER'),
    'determineRouteBeforeAppMiddleware' => getenv('APP_DETERMINE_ROUTE_BEFORE_APP_MIDDLEWARE'),
    'displayErrorDetails' => getenv('APP_DEBUG'),
    'address' => getenv('APP_URL'),

    'middleware' => [
        App\Middleware\AuthenticationMiddleware::class,
    ],

    'providers' => [
        \Easychat\Providers\ServiceProviders::class,
        \Easychat\Providers\ControllerProviders::class,

        // user provider
        \App\Providers\MiddlewareProviders::class
    ]
];
