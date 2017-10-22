<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class MultiSelectFilterAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        '/js/multi_select_filter.js',
    ];
    public $depends = [
        'dosamigos\multiselect\MultiSelectAsset',
    ];
}
