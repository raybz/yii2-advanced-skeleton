<?php

return [
    'components' => [
        /**
         * 后台数据库
         */
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
            'dsn' => 'mysql:host=10.66.92.160;dbname=mdata_2144_cn',
            'username' => 'mdata_2144_cn',
            'password' => 'BfqxzFzxW2Qz4F#',
        ],

        /**
         * Redis 缓存
         */
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => '10.66.226.18',
                'port' => 6379,
                'database' => 10,
                'password' => 'crs-nquodqog:MdateRedis#0707',
            ],
        ],

        /**
         * 统计Redis
         * database 0
         */
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '10.66.226.18',
            'port' => 6379,
            'database' => 0,
            'password' => 'crs-nquodqog:MdateRedis#0707',
        ],
    ],
];
