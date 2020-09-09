<?php
namespace app\modules\pmnbd\controllers;

use Yii;
use frontend\models\FormHelp;
use yii\web\Controller;

class FormController extends Controller
{
	public function actionValidate()
	{
		$q= print_r(Yii::$app->request->post(), true);
		file_put_contents('/var/www/pmnetwork/pmnetwork_dima/frontend/modules/pmnbd/log.txt', $q);
		return true;

	}

	/**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}