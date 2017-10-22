<?php

namespace Components\Traits;

trait SingletonTrait
{
    protected static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = \Yii::createObject([
                'class' => static::class,
            ]);
        }

        return static::$instance;
    }
}
