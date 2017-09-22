<?php

namespace Components;

use yii\helpers\ArrayHelper;

class BaseDefinition
{
    public static $labels = [];

    public static function getLabel($def, $default = '')
    {
        return isset(static::$labels[$def]) ? static::$labels[$def] : $default;
    }

    public static function getLabels($withAll = false, $allLabel = '全部')
    {
        $labels = static::$labels;
        if ($withAll) {
            $labels = ArrayHelper::merge(['' => $allLabel], $labels);
        }

        return $labels;
    }
}
