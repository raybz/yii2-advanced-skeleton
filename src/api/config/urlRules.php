<?php
return [
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'v1/game',
        'extraPatterns' => [
            'GET <id:[a-zA-Z0-9\_\-\,]+>' => 'view',
        ],
    ],
    '<module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>' => '<module>/<controller>/<action>',
    '<module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<module>/<controller>/<action>',
];