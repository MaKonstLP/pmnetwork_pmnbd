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

    public function init() {
        $this->css = $this->getVersionedFiles($this->css);
        $this->js = $this->getVersionedFiles($this->js);
        parent::init();
        // resetting BootstrapAsset to not load own css files
        \Yii::$app->assetManager->bundles['yii\\bootstrap\\BootstrapAsset'] = [
            'css' => [],
            'js' => []
        ];
    }

    public function getVersionedFiles($files)
    {
        $out = [];
        foreach ($files as $file) {
            $filePath = \Yii::getAlias($this->sourcePath. '/' . $file);
            $out[] = $file . (is_file($filePath) ? '?v=' . filemtime($filePath) : '');
        }
        return $out;
    }
}
