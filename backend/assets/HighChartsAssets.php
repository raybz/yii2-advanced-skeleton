<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class HighChartsAssets extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '//cdn.bootcss.com/highcharts/5.0.7/css/highcharts.css'
    ];
    public $js = [
        '//cdn.bootcss.com/highcharts/5.0.7/js/highcharts.js',
        '/js/hcharts.js'

    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
