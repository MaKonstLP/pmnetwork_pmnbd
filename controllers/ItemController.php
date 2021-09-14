<?php

namespace app\modules\pmnbd\controllers;

use common\models\Restaurants;
use common\models\Seo;
use frontend\modules\pmnbd\components\Breadcrumbs;
use frontend\modules\pmnbd\models\ElasticItems;
use yii\web\NotFoundHttpException;

class ItemController extends BaseFrontendController
{

	public function actionIndex($restSlug, $roomSlug = null)
	{

		$model = ElasticItems::find()->query([
			'bool' => [
				'must' => [
					['match' => ['restaurant_gorko_id' =>452629]],
					//['match' => ['restaurant_city_id' => \Yii::$app->params['subdomen_id']]],
				],
			]
		])->one();
		//var_dump($model);exit;
		$rest_item = ElasticItems::find()->query([
			'bool' => [
				'must' => [
					['match' => ['restaurant_slug' => $restSlug]],
					['match' => ['restaurant_city_id' => \Yii::$app->params['subdomen_id']]],
				],
			]
		])->one();
		
		if(empty($rest_item)) {
			throw new NotFoundHttpException();
		}
		$rooms = $rest_item['rooms'];
		$rooms_price_arr = [];
		$rooms_capacity_arr = [];
		$rooms_type_arr = [];

		foreach ($rooms as $key => $value) {
			array_push($rooms_price_arr, $value['price']);
			$rooms_capacity_arr[$value['slug']] = $value['capacity'];
			$rooms_type_arr[$value['type_name']] = $value['name'];
		}

		asort($rooms_capacity_arr);

		$other_rests = ElasticItems::find()->limit(20)->query([
			'bool' => [
				'must' => [
					['match' => ['restaurant_district' => $rest_item->restaurant_district]]
				],
				'must_not' => [
					['match' => ['restaurant_id' => $rest_item->restaurant_id]]
				],
			],
		])->all();
		shuffle($other_rests);
		$other_rests = array_slice($other_rests, 0, 7);

		if (!empty($roomSlug)) {
			$same_objects = array_filter($rooms, function($room) use ($roomSlug){
				return $room['slug'] != $roomSlug;
			});
			$room = current(array_diff_key($rooms, $same_objects));
			if (empty($room)) {
				throw new NotFoundHttpException();
			}
			$seo = (new Seo('room', 1, 0, (object)$room, 'room', $rest_item))->seo;
			$seo['breadcrumbs'] = Breadcrumbs::get_rooom_crumbs($rest_item);
			$seo['address'] = $rest_item->restaurant_address;
			$this->setSeo($seo);
			$other_rooms = array_reduce($other_rests, function ($acc, $rest) {
				return array_merge($acc, $rest['rooms']);
			}, []);

			return $this->render('index.twig', array(
				'item' => $room,
				'rest_item' => $rest_item,
				'seo' => $seo,
				'same_objects' => $same_objects,
				'other_rooms' => $other_rooms
			));
		}

		$seo = (new Seo('item', 1, 0, $rest_item, 'rest'))->seo;
		$seo['breadcrumbs'] = Breadcrumbs::get_restaurant_crumbs($rest_item);
		$seo['address'] = $rest_item->restaurant_address;
		$this->setSeo($seo);
		
		return $this->render('rest_index.twig', array(
			'item' => $rest_item,
			'min_price' => ($filtered = array_filter($rooms_price_arr)) ? min($filtered) : 0,
			'rooms_capacity' => $rooms_capacity_arr,
			'seo' => $seo,
			'same_objects' => $rooms,
			'other_rests' => $other_rests,
		));

	}

	private function setSeo($seo){
        $this->view->title = $seo['title'];
        $this->view->params['desc'] = $seo['description'];
        $this->view->params['kw'] = $seo['keywords'];
    }
	
}
