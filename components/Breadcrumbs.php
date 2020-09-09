<?php

namespace frontend\modules\pmnbd\components;


class Breadcrumbs {
	public static function get_breadcrumbs($level, $rest = null) {
		switch ($level) {
			case 1:	
				$breadcrumbs=[
					'/' => 'Главная',
				];
				break;
			case 2:
				$breadcrumbs=[
                    '/' => 'Главная',
                    '/catalog/' => 'Рестораны в ' . \Yii::$app->params['subdomen_dec'],
                ];
                break;
            case 3:
                $breadcrumbs=[
                    '/' => 'Главная',
                    '/catalog/' => 'Рестораны в ' . \Yii::$app->params['subdomen_dec'],
                ];
                if($rest) {
                    $breadcrumbs["/catalog/restoran-$rest->restaurant_slug/"] = $rest->restaurant_name;
                }
                break;

		}
		return $breadcrumbs;
	}
}