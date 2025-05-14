<?php


use Laravel\Sanctum\Sanctum;

return [

    'stateful' => [
        'localhost',
        '127.0.0.1',
        'betamaps.admsurgut.ru',
        'backend', // имя контейнера бекенда
    ],

    'guard' => ['api'],

    'expiration' => null,

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];
