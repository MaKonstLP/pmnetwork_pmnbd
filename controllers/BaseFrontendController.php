<?php

namespace app\modules\pmnbd\controllers;

use yii\web\Controller;

abstract class BaseFrontendController extends Controller
{
    public function beforeAction($action)
    {
        if (\Yii::$app->request->cookies->get('basic_auth')) {
            return parent::beforeAction($action);
        }

        if (
            empty($_SERVER['PHP_AUTH_USER'])
            || empty($_SERVER['PHP_AUTH_PW'])
            || ($_SERVER['PHP_AUTH_USER'] != 'l1der' && $_SERVER['PHP_AUTH_PW'] != 'l1der')
        ) {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'unauthorized';
            exit;
        }

        \Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'basic_auth',
            'value' => 'auth',
            'expire' => time() + 86400 * 30,
            'domain' => '.' . explode(":", \Yii::$app->params['siteAddress'])[0]
        ]));
        
        return parent::beforeAction($action);
    }
    
}
