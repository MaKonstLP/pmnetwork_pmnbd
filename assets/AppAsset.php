<?php

namespace frontend\modules\pmnbd\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/pmnbd/web/dist';
    //public $baseUrl = '@web';
    //public $basePath = '@frontend/modules/svadbanaprirode/web';
    public $css = [
        'css/app.min.css',
    ];
    public $js = [
        'js/app.min.js',
    ];
    public $depends = [
        //'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
    ];
    public $publishOptions = [
        'forceCopy' => true,
    ];
}
