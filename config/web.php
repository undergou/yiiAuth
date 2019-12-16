<?php

$params = require __DIR__.'/params.php';
$db = require __DIR__.'/db.php';

$config = [
    'id'         => 'basic',
    'name'       => 'User Auth',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],
    'aliases'    => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request'              => [
            'cookieValidationKey' => 'zDQyQ3SbZolKCJn9CE7RHqWJ7LmPMMcy',
        ],
        'cache'                => [
            'class' => 'yii\caching\FileCache',
        ],
        'user'                 => [
            'identityClass'   => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'authManager'          => [
            'class' => 'yii\rbac\DbManager',
        ],
        'errorHandler'         => [
            'errorAction' => 'site/error',
        ],
        'mailer'               => [
            'class'     => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
//            'transport' => [
//                'class'      => 'Swift_SmtpTransport',
//                'host'       => 'smtp.mail.ru',
//                'username'   => 'lamer_10@mail.ru',
//                'password'   => 'bphfzdhfq',
//                'port'       => '465',
//                'encryption' => 'ssl',
//            ],

        ],
        'log'                  => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db'                   => $db,
        'urlManager'           => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [
                'login'             => 'site/login',
                'register'          => 'site/register',
                'retrieve-password' => 'site/retrieve-password',
                'change-password'   => 'site/change-password',
            ],
        ],
        'authClientCollection' => [
            'class'   => 'yii\authclient\Collection',
            'clients' => [
                'facebook'  => [
                    'class'          => 'yii\authclient\clients\Facebook',
                    'authUrl'        => 'https://www.facebook.com/dialog/oauth?display=popup',
                    'clientId'       => '',
                    'clientSecret'   => '',
                    'attributeNames' => ['name', 'email'],
                ],
                'google'    => [
                    'class'        => 'yii\authclient\clients\Google',
                    'clientId'     => '',
                    'clientSecret' => '',
                ],
                'vkontakte' => [
                    'class'        => 'yii\authclient\clients\VKontakte',
                    'clientId'     => '',
                    'clientSecret' => '',
                ],
            ],
        ],
    ],
    'params'     => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class'      => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
