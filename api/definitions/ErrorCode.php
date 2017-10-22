<?php

namespace api\definitions;

use Components\BaseDefinition;

class ErrorCode extends BaseDefinition
{
    const SUCCESS = 200;
    const FAILED = 400;
    const UNAUTHORIZED = 403;
    const NOT_FOUND = 404;

    public static $labels = [
        self::SUCCESS => '成功',
        self::FAILED => '失败',
        self::UNAUTHORIZED => '校验失败',
        self::NOT_FOUND => '未找到',
    ];
}
