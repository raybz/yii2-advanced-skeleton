<?php
$params = array_merge(
    require(__DIR__.'/../../common/config/params.php'),
    require(__DIR__.'/../../common/config/params-local.php'),
    require(__DIR__.'/params.php'),
    require(__DIR__.'/params-local.php')
);

return [
    'id' => 'scp.2144.cn',
    'name' => 'SCP',
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',
    'timezone' => 'Asia/Shanghai',
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        "admin" => [
            "class" => \mdm\admin\Module::class,
//            'layout' => 'left-menu'
        ],
    ],
    'as access' => [
        'class' => \mdm\admin\components\AccessControl::class,
        'allowActions' => [
            'site/login',
            'site/error',
            'site/logout',
            'site/idlogin',
            'site/idregister',
            'api/*',
            'task/*',
        ],
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            "defaultRoles" => ["guest"],
        ],

//        'audit' => [
//            'class' => \Components\Audit::class,
//            'systemId' => 38,
//            'authKey' => 'axBYLfrcXs5YJynz2daoajAQtQ0NpRZD',
//        ],
//        'idauth' => [
//            'class' => \Components\IdAuth::class,
//            'systemId' => 38,
//            'authKey' => 'Xnl3moNh9WdIavNRy7mp6wzzY3C27C4d',
//        ],
        'request' => [
            'csrfParam' => '_csrf-backend-scp',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            /** urlRules */
            'rules' => require __DIR__.'/urlRules.php',
        ],
        'user' => [
            'identityClass' => 'backend\models\Admin',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            'class' => \yii\redis\Session::class,
        ],
        /**
         * debug开启日志
         */
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];
