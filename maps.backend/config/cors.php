<?php

return [

    'paths' => ['api/*', 'login', 'api/logout', 'logout','sanctum/csrf-cookie', 'sanctum/csrf-cookie/'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://betamaps.admsurgut.ru',
        'http://localhost:3000',
        'http://localhost'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
