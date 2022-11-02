<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\helpers\Html;
use common\components\GorkoCalltracking;

class CalltrackingController extends Controller
{
	public function beforeAction($action)
	{
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}

	public function actionSend()
	{
		if (isset($_POST['phone']) && !empty($_POST['phone'])) {
			$payload = [];

			$payload['phone'] = preg_replace('~[^\d\+]~', '', $_POST['phone']);
			$payload['channel'] = 'birthday-place';
			$payload['site'] = $_SERVER['HTTP_HOST'];
			$payload['gaClientId'] = $_POST['clientId'];
			$payload['refFirst'] = $_COOKIE['a_ref_0'] ?? '';
			$payload['refLast'] = $_COOKIE['a_ref_1'] ?? '';
			$payload['utmFirst'] = $_COOKIE['a_utm_0'] ?? '';
			$payload['utmLast'] = $_COOKIE['a_utm_1'] ?? '';

			$payload = json_encode($payload);

			$resp = GorkoCalltracking::send_lead($payload);

			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			return $resp;
		} else {
			return 1;
		}
	}
}
