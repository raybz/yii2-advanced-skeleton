<?php
$params = array_merge(
    require(__DIR__.'/../../common/config/params.php'),
    require(__DIR__.'/../../common/config/params-local.php'),
    require(__DIR__.'/params.php'),
    require(__DIR__.'/params-local.php')
);
return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',
    'timezone' => 'Asia/Shanghai',
    'bootstrap' => ['log'],
    'modules' => require __DIR__.'/modules.php',
    'components' => [
        /**
         * rest返回值
         */
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if (!$response->isSuccessful) {
                    if (isset($response->data['message'])) {
                        $response->data = [
                            'code' => $response->statusCode,
                            'message' => $response->data['message'],
                            'data' => [],
                            'timestamp' => time(),
                        ];
                    }
                }
            },
        ],
        'errorHandler' => [
            'errorAction' => 'v1/site/error',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'user' => [
            'identityClass' => 'api\modules\v1\models\Auth',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => require __DIR__.'/urlRules.php',
        ],
    ],
    'params' => $params,
];