<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Rooms;
use common\models\elastic\ItemsElastic;
use frontend\modules\pmnbd\components\Breadcrumbs;
use frontend\modules\pmnbd\components\Declension;
use common\models\elastic\ItemsWidgetElastic;
use app\modules\pmnbd\models\ElasticItems;
use yii\web\NotFoundHttpException;

class ItemController extends Controller
{

	public function actionIndex($restSlug, $roomSlug = null)
	{

		$rest_item = ElasticItems::find()->query([
			'bool' => [
				'must' => [
					['match' => ['restaurant_slug' => $restSlug]],
					['match' => ['restaurant_city_id' => \Yii::$app->params['subdomen_id']]],
				],
			]
		])->one();

		/*echo "<pre>";
		var_dump($rest_item);
		exit;*/
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

		$other_rests = ElasticItems::find()->limit(5)->query([
			'bool' => [
				'must' => [
					['match' => ['restaurant_district' => $rest_item->restaurant_district]]
				],
				'must_not' => [
					['match' => ['restaurant_id' => $rest_item->restaurant_id]]
				],
			],
		])->all();

		if (isset($roomSlug)) {
			foreach ($rooms as $key => $room) {
				if ($room['slug'] == $roomSlug) {
					$item = $room;
					unset($rooms[$key]);
					break;
				}
			}
			if (!isset($item)) {
				throw new NotFoundHttpException();
			}
			$seo['h1'] = $item['name'] . ' в ресторане ' . $rest_item->restaurant_name;
			$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(3, $rest_item);
			$seo['desc'] = $rest_item->restaurant_name;
			$seo['address'] = $rest_item->restaurant_address;

			$other_rooms = array_reduce($other_rests, function ($acc, $rest) {
				return array_merge($acc, $rest['rooms']);
			}, []);

			return $this->render('index.twig', array(
				'item' => $item,
				'rest_item' => $rest_item,
				'queue_id' => $item['id'],
				'seo' => $seo,
				'same_objects' => $rooms,
				'other_rooms' => $other_rooms
			));
		}

		$seo['h1'] = 'Ресторан ' . $rest_item->restaurant_name . ' в ' . Yii::$app->params['subdomen_dec'];
		$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(2);
		$seo['desc'] = $rest_item->restaurant_name;
		$seo['address'] = $rest_item->restaurant_address;

		//$itemsWidget = new ItemsWidgetElastic;
		//$other_rooms = $itemsWidget->getOther($rest_item->restaurant_id, $id);

		
		/*echo $rest_item->restaurant_district;
		echo "<pre>";
		var_dump($other_rests);
		exit;*/

		$parking = 'Нет';
		if (!empty($rest_item->restaurant_parking)) {
			preg_match('~(\d+)~ims', $rest_item->restaurant_parking, $match);
			$parking = $rest_item->restaurant_parking . ' мест'.Declension::get_num_ending($rest_item->restaurant_parking,['о','а','']);
		}

					//echo "<pre>";var_dump($rest_item);exit;
		return $this->render('rest_index.twig', array(
			'item' => $rest_item,
			'min_price' => min(array_filter($rooms_price_arr)),
			'rooms_capacity' => $rooms_capacity_arr,
			'queue_id' => $roomSlug,
			'seo' => $seo,
			'same_objects' => $rooms,
			'other_rests' => $other_rests,
			'parking' => $parking,
			'text' => $text
		));

		//$item = ApiItem::getData($item->restaurants->gorko_id);


	}
}
