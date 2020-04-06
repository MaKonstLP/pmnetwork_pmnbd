<?php

namespace app\modules\pmnbd;


use Yii;
/**
 * svadbanaprirode module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\pmnbd\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        //Yii::$app->setLayoutPath('@app/modules/svadbanaprirode/layouts');
        //Yii::$app->layout = 'svadbanaprirode';
        //$this->viewPath = '@app/modules/svadbanaprirode/views/';
        parent::init();
        //$this->viewPath = '@app/modules/svadbanaprirode/views/';


        // custom initialization code goes here
    }
}
