<?php

return [
    'name' => 'EstudioContable SaaS',
    'url' => getenv('APP_URL') ?: 'http://localhost/estudiocontable',
    'base_path' => getenv('APP_BASE_PATH') ?: '/',
    'debug' => filter_var(getenv('APP_DEBUG') ?: 'true', FILTER_VALIDATE_BOOLEAN),
    'timezone' => 'America/Argentina/Buenos_Aires',
    'encryption_key' => getenv('APP_ENCRYPTION_KEY') ?: 'CHANGE_THIS_KEY_IN_PRODUCTION_32B!',
    'csrf_token_name' => 'csrf_token',
    'session' => [
        'lifetime' => 3600,
        'name' => 'estudio_session',
    ],
    'rate_limit' => [
        'login_max_attempts' => 5,
        'login_decay_minutes' => 15,
    ],
];