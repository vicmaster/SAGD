<?php
return [
        /*
         *      |--------------------------------------------------------------------------
         *      | Laravel CORS
         *      |--------------------------------------------------------------------------
         *      |
         *      | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
         *      | to accept any value, the allowed methods however have to be explicitly listed.
         *      |
         *
         */
        'supportsCredentials' => false,
        'allowedOrigins' => ['*'],
        'allowedHeaders' => ['*'],
        'allowedMethods' => ['GET', 'POST', 'PUT',  'DELETE', 'OPTIONS'],
        'exposedHeaders' => [],
        'maxAge' => 1728000,
        'hosts' => [],
];
