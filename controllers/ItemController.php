<?php

namespace app\modules\pmnbd\controllers;

use Yii;
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
					['match' => ['restaurant_gorko_id' => 452629]],
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

		if (empty($rest_item)) {
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
			$same_objects = array_filter($rooms, function ($room) use ($roomSlug) {
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

			if ($rest_item->restaurant_premium) Yii::$app->params['premium_rest'] = true;

			// ===== schemaOrg Product START =====
			$this->setSchema($rest_item, $room);
			// ===== schemaOrg Product END =====

//            echo '<pre>';
//            print_r($room);
//            die();

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

		// ===== schemaOrg Product START =====
		$this->setSchema($rest_item);
		// ===== schemaOrg Product END =====

		// echo ('<pre>');
		// print_r($rest_item);
		// exit;

        return $this->render('rest_index.twig', array(
			'item' => $rest_item,
			'min_price' => ($filtered = array_filter($rooms_price_arr)) ? min($filtered) : 0,
			'rooms_capacity' => $rooms_capacity_arr,
			'seo' => $seo,
			'same_objects' => $rooms,
			'other_rests' => $other_rests,
            'count_rooms' => count($rest_item['rooms'])
		));
	}

    private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}

	private function setSchema($rest, $room = false)
	{
		$name = (isset($room) && !empty($room)) ? $room["name"] . " в ресторане '" . $rest->restaurant_name . "'" : $rest->restaurant_name;

		$json_str = '';
		$json_str .= '{
				"@context": "https://schema.org",
				"@type": [
					"Apartment",
					"Product"
				],
				"name": "' . $name . '"';

		if (isset($room) && !empty($room)) {
			$json_str .= ',';
			$json_str .= '
				"offers": {
					"@type": "AggregateOffer",
					"priceCurrency": "RUB",
					"highPrice": "' . $room['price'] . '",
					"lowPrice": "' . $room['price'] . '"
				}';
		} elseif ($rest->restaurant_max_check) {
			$json_str .= ',';
			$json_str .= '
				"offers": {
					"@type": "AggregateOffer",
					"priceCurrency": "RUB",
					"highPrice": "' . $rest->restaurant_max_check . '",
					"lowPrice": "' . $rest->restaurant_min_check . '"
				}';
		}

		if (isset($rest->restaurant_rev_ya['count']) && $rest->restaurant_rev_ya['count'] && $rest->restaurant_rev_ya['rate']) {
			$json_str .= ',';
			$json_str .= '
				"aggregateRating": {
					"@type": "AggregateRating",
					"bestRating": "5",
					"reviewCount": "' . preg_replace('/[^0-9]/', '', $rest->restaurant_rev_ya['count']) . '",
					"ratingValue": "' . $rest->restaurant_rev_ya['rate'] . '"
				}';
		}
		$json_str .= '}';

		Yii::$app->params['schema_product'] = $json_str;
	}
}
