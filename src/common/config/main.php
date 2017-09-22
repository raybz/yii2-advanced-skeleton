<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [

        'formatter' => [
            'dateFormat' => 'Y-m-d',
            'datetimeFormat' => 'Y-m-d H:i:s',
            'timeFormat' => 'H:i:s',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CNY',
            'locale' => 'zh-CN',
//            'defaultTimeZone' => 'Asia/Shanghai'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],

];
