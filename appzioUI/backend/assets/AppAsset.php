<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/jquery-ui.min.css',
        'daterangepicker/daterangepicker.css',
    ];
    public $js = [
        'js/grid-handler.js',
        'js/form-handler.js',
        'daterangepicker/moment.min.js',
        'daterangepicker/daterangepicker.js',
        'js/jquery-ui.min.js',
        'js/jquery.repeater.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}