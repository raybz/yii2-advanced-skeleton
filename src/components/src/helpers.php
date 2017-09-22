<?php

use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @param string $key
 * @param mixed  $default
 *
 * @return mixed
 */
function env($key, $default = false)
{
    $value = getenv($key);

    if ($value === false) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;
    }

    return $value;
}

/**
 * @see Url::to()
 *
 * @param string|array $url
 * @param bool         $scheme
 *
 * @return string
 */
function url($url = '', $scheme = false)
{
    return Url::to($url, $scheme);
}

/**
 * @param string $text
 * @param string $type
 */
function flash($text, $type = 'success')
{
    Yii::$app->session->setFlash($type, $text);
}

/**
 * @param Model|Model[] $models
 *
 * @return string
 */
function form_error($models)
{
    return Html::errorSummary($models, [
        'class' => 'am-alert am-alert-danger',
        'header' => '<button type="button" class="am-close">&times;</button>',
    ]);
}
