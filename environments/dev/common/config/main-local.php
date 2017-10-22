<?php
return [
    'components' => [
        /**
         * 后台数据库
         */
        'db' => [
            'class'     => 'yii\db\Connection',
            'charset'   => 'utf8',
            'dsn' => 'mysql:host=127.0.0.1;dbname=scp_2144_cn',
            'username' => 'root',
            'password' => '123456',
        ],

        /**
         * Redis 缓存
         */
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => '127.0.0.1',
                'port' => 6379,
                'database' => 1
            ]
        ],

        /**
         * 统计Redis
         * database 0
         */
        'redis'=>[
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ],
    ],
];
