<?php

use Dotenv\Dotenv;

//Start ENV
$dotenv = Dotenv::createImmutable(dirname(__DIR__) . '/config');
$dotenv->load();

//Check empty env
$dotenv->required(['NOSO_PAY', 'CLAIM_TIME', 'MIN_NOSO_PAYMENTS', 'PERCENT_REF'])->notEmpty();

return [
    'settings' => [
        'addContentLengthHeader' => false,
        'displayErrorDetails' => true,
        'db' => [
            'Host' => $_ENV['FAUCET_DATABASE_HOST'],
            'Database' => $_ENV['FAUCET_DATABASE'],
            'Username' => $_ENV['FAUCET_DATABASE_USER'],
            'Password' => $_ENV['FAUCET_DATABASE_PASSWORD']
        ],
        'recaptcha' => [
            'PublicKey' => $_ENV['PUBLIC_KEY_RE'],
            'PrivateKey' => $_ENV['PRIVATE_KEY_RE']
        ]
    ]

];
