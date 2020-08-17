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
use common\components\Breadcrumbs;
use common\models\elastic\ItemsWidgetElastic;
use app\modules\pmnbd\models\ElasticItems;
use yii\web\NotFoundHttpException;

class ItemController extends Controller
{

	public function actionIndex($restId, $id=null)
	{
		
		//ItemsElastic::refreshIndex();
		//exit;

		$rest_item = ElasticItems::get($restId);
		/*echo "<pre>";
		var_dump($rest_item);
		exit;*/
		$rooms = $rest_item['rooms'];
		$rooms_price_arr = [];
		$rooms_capacity_arr = [];
		$rooms_type_arr = [];
		foreach ($rooms as $key => $value) {
			array_push($rooms_price_arr, $value['price']);
			$rooms_capacity_arr[$value['id']] = $value['capacity'];
			$rooms_type_arr[$value['type_name']] = $value['name'];
		}

		asort($rooms_capacity_arr);

		if (isset($id)) {
			foreach ($rooms as $key => $room) {
				if ($room['id'] == $id) {
					$item = $room;
					unset($rooms[$key]);
					break;
				}
			}
			if (!isset($item)){
				throw new NotFoundHttpException();
			}
			$seo['h1'] = $item['name'] . ' в ресторане ' . $rest_item->restaurant_name;
			$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(2);
			$seo['desc'] = $rest_item->restaurant_name;
			$seo['address'] = $rest_item->restaurant_address;

			$itemsWidget = new ItemsWidgetElastic;
			$other_rooms = $itemsWidget->getOther($rest_item->restaurant_id, $id);

			return $this->render('index.twig', array(
				'item' => $item,
				'queue_id' => $id,
				'seo' => $seo,
				'same_objects' => $rooms,
				'other_rooms' => $other_rooms
			));

		}

		$seo['h1'] = 'Ресторан ' . $rest_item->restaurant_name . ' в Санкт-Петербурге' ;
		$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(2);
		$seo['desc'] = $rest_item->restaurant_name;
		$seo['address'] = $rest_item->restaurant_address;

		//$itemsWidget = new ItemsWidgetElastic;
		//$other_rooms = $itemsWidget->getOther($rest_item->restaurant_id, $id);

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
		/*echo $rest_item->restaurant_district;
		echo "<pre>";
		var_dump($other_rests);
		exit;*/

		$parking = 'Нет';
		if (!empty($rest_item->restaurant_parking)) {
			preg_match('~(\d+)~ims', $rest_item->restaurant_parking, $match);
			$parking = (isset($match[1])&&($match[1] != 0) ? $match[1] . ' авто' : 'Есть');
		}

		#Временно
		$text = '<p>Загородный комплекс  Истра Holiday  расположен в 45 км к северо-западу от Москвы. Рядом с комплексом находится Истринское водохранилище и смешанный лес.</p>
					<p>Совсем рядом расположен и комплекс Лада Holiday, где также можно организовать событие вашей мечты!</p>
					<h2>Преимущества:</h2>
					<ul>
					<li>Живописные локации для проведения выездной регистрации у воды и свадебной съемки</li>
					<li>Различные развлечения на территории комплекса: караоке, бильярд, спа</li>
					<li>Детская комната и спортивные развлечения на свежем воздухе для детей</li>
					</ul>
					<h2>Подарки на свадебные банкеты (от 15 гостей):</h2>
					<ul>
					<li>Номер для молодоженов (стандарт улучшенный)</li>
					<li>Аренда зала/площадки для проведения банкета (кроме «Павильон на воде», зала «Весна»)</li>
					<li>Скидка 10% на номера для гостей</li>
					<li>Комплимент от отеля: вино и фруктовая ваза</li>
					</ul>
					<h2>Подробности уточняйте у банкетного менеджера!</h2>';
		#Временно
		return $this->render('rest_index.twig', array(
			'item' => $rest_item,
			'min_price' => min($rooms_price_arr),
			'rooms_capacity' => $rooms_capacity_arr,
			'queue_id' => $id,
			'seo' => $seo,
			'same_objects' => $rooms,
			'other_rests' => $other_rests,
			'parking' => $parking,
			'text' => $text
		));
		
		//$item = ApiItem::getData($item->restaurants->gorko_id);

		
	}
}