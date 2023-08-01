<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use common\models\GorkoApiTest;
use common\models\Subdomen;
use common\models\Slices;
use common\models\Restaurants;
use common\models\MetroStations;
use common\models\RestaurantsYandex;
use common\models\RestaurantsModule;
use common\models\RestaurantSlug;
use common\models\RestaurantsLocation;
use common\models\RestaurantsTypes;
use common\models\RestaurantsPremium;
use common\models\YandexReview;
use frontend\modules\pmnbd\models\ElasticItems;
use yii\web\Controller;
use common\components\AsyncRenewRestaurants;
use common\models\FilterItems;
// use common\models\siteobject\SiteObject;
// use common\models\siteobject\SiteObjectSeo;
use common\models\elastic\FilterQueryConstructor;
use common\models\elastic\FilterQueryConstructorElastic;
use common\models\Images;
use common\models\Rooms;
use frontend\modules\pmnbd\models\SubdomenFilteritem;
use backend\modules\pmnbd\models\blog\BlogPost;
use backend\modules\pmnbd\models\blog\BlogTag;
use backend\modules\pmnbd\models\blog\BlogPostSubdomen;
use backend\modules\pmnbd\models\blog\BlogPostSlice;
use backend\modules\pmnbd\models\Metros;
use yii\helpers\ArrayHelper;
use common\models\Filter;
use common\models\SlicesMetroExtra;
use frontend\components\QueryFromSlice;
use frontend\modules\pmnbd\components\UpdateFilterItems;
use common\models\elastic\ItemsFilterElastic;
use backend\modules\pmnbd\models\siteobject\SiteObject;
use backend\modules\pmnbd\models\siteobject\SiteObjectSeo;
use common\models\Pages;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class TestController extends BaseFrontendController
{
	public function actionSendmessange()
	{
		$to = ['zadrotstvo@gmail.com'];
		$subj = "Тестовая заявка";
		$msg = "Тестовая заявка";
		$message = $this->sendMail($to, $subj, $msg);
		var_dump($message);
		exit;
	}

	public function actionIndex()
	{
		/* $rests = RestaurantsModule::find()->all();
		foreach ($rests as $key => $rest) {
			$rest_item = ElasticItems::find()->query([
				'bool' => [
					'must' => [
						['match' => ['restaurant_gorko_id' => $rest->id]],
					],
				]
			])->one();

			if(isset($rest_item->restaurant_name)){
				$rest->name = $rest_item->restaurant_name;
				$rest->address = $rest_item->restaurant_address;
				$rest->save();
			}
			
		} */

		/* $connection = new \yii\db\Connection([
			'username' => 'root',
			'password' => 'GxU25UseYmeVcsn5Xhzy',
			'charset'  => 'utf8mb4',
			'dsn' => 'mysql:host=localhost;dbname=pmn'
		]);
		$connection->open();
		Yii::$app->set('db', $connection);

		$restaurant_ya_model = RestaurantsYandex::find()->where(['id' => 2])->one();

		$restaurants = Restaurants::find()
		->with('yandexReview')
		->where(['active' => 1, 'commission' => 2])
		->limit(100000)
		->all();

		foreach ($restaurants as $key => $restaurant) {
			echo ('<pre>');
			print_r($restaurant->yandexReview->rev_ya_id);
			echo ('<pre>');
		print_r($restaurant->yandexReview->rev_ya_rate);
		echo ('<pre>');
		print_r($restaurant->yandexReview->rev_ya_count);
			exit;
			
		}

		echo ('<pre>');
		print_r($restaurants);
		exit;

		echo ('<pre>');
		print_r($restaurant_ya_model->rev_ya_id);
		echo ('<pre>');
		print_r($restaurant_ya_model->rev_ya_rate);
		echo ('<pre>');
		print_r($restaurant_ya_model->rev_ya_count);
		exit; */
		/* if (file_exists('https://geocode-maps.yandex.ru/1.x/?geocode=61.442760467529,55.183074951172&apikey=84c84f73-da0f-4da5-ab73-c91c0c210954&sco=longlat&kind=metro&ll=61.442760467529,55.183074951172&spn=0.04,0.04&format=json&results=3')) {
			echo ('<pre>');
			print_r(2222);
			exit;
		} else {
			echo ('<pre>');
			print_r(3333);
			exit;
		} */

		// $restraurantCoordinates = $restaurant->longitude . ',' . $restaurant->latitude;
		// longitude = 61.442760467529;
		// latitude = 55.183074951172;

		// 61.442760467529,55.183074951172

		/* $apiAnswer = json_decode(file_get_contents('https://geocode-maps.yandex.ru/1.x/?geocode=61.442760467529,55.183074951172&apikey=84c84f73-da0f-4da5-ab73-c91c0c210954&sco=longlat&kind=metro&ll=61.442760467529,55.183074951172&spn=0.04,0.04&format=json&results=3'));

		echo ('<pre>');
		print_r($apiAnswer);
		exit; */

		//* ======== обновление таблицы "restaurants_yandex" в общей БД START ========
		/* $connection = new \yii\db\Connection([
			'username' => 'root',
			'password' => 'GxU25UseYmeVcsn5Xhzy',
			'charset'  => 'utf8mb4',
			'dsn' => 'mysql:host=localhost;dbname=pmn'
		]);
		$connection->open();
		Yii::$app->set('db', $connection);

		//* добавление новых рестов
		$restaurants_ya_model = RestaurantsYandex::find()->all();

		$restaurants = Restaurants::find()
			->with('yandexReview')
			->where(['>', 'id', 15984])
			->limit(100000)
			->all();

		foreach ($restaurants as $key => $rest) {
			$rest_ya_exist = false;
			foreach ($restaurants_ya_model as $key => $rest_ya) {
				if ($rest->gorko_id == $rest_ya->gorko_id) {
					$rest_ya_exist = true;
				}
			}

			if (!$rest_ya_exist) {
				$model = new RestaurantsYandex();
				$model->gorko_id = $rest->gorko_id;
				$model->name = $rest->name;
				$model->address = $rest->address;
				$model->latitude = $rest->latitude;
				$model->longitude = $rest->longitude;
				$model->district = $rest->district;
				$model->parent_district = $rest->parent_district;
				$model->city_id = $rest->city_id;
				$model->phone = $rest->phone;
				$model->commission = $rest->commission;
				$model->active = $rest->active;
				$model->save();
			}
		} */
		//* ======== обновление таблицы "restaurants_yandex" в общей БД END ========



		/* $collectionPosts = BlogPost::findWithMedia()
			->with('blogPostTags')
			// ->joinWith('blogPostSubdomens')
			->joinWith('blogPostSlices')
			->where(['published' => true])
			->andWhere(['collection' => true])
			// ->andWhere([BlogPostSubdomen::tableName() . '.subdomen_id' => Yii::$app->params['subdomen_id']])
			->andWhere([BlogPostSlice::tableName() . '.slice_id' => 2])
			->andWhere([BlogPostSlice::tableName() . '.subdomen_id' => Yii::$app->params['subdomen_id']])
			// ->andWhere(['!=', 'id', $post->id])
			// ->andWhere(['<', 'published_at', $post->published_at])
			// ->orderBy(['published_at' => SORT_DESC])
			->limit(10)
			->all();

		echo ('<pre>');
		print_r($collectionPosts);
		exit; */


		// $metros = Metros::find()->where(['LIKE', 'same_station_table_id', '%,%'])->all();
		/* $metros = Metros::find()
			->where(['LIKE', 'same_station_table_id', ','])
			->all();

		foreach ($metros as $key => $metro) {
			$same_table_ids = explode(',', $metro['same_station_table_id']);
			if (in_array(2822, $same_table_ids)) {
				echo ('<pre>');
				print_r(2222);
				exit;
			}

			echo ('<pre>');
			print_r($same_table_ids);
			exit;
		} */

		// echo ('<pre>');
		// print_r($metros_cities);
		// exit;





		//** ======== выгрузка рестов по всем городоам в таблицу excel START ========
		/* $connection = new \yii\db\Connection([
			'username' => 'root',
			'password' => 'GxU25UseYmeVcsn5Xhzy',
			'charset'  => 'utf8mb4',
			'dsn' => 'mysql:host=localhost;dbname=pmn'
		]);
		$connection->open();
		Yii::$app->set('db', $connection);

		$restaurants = Restaurants::find()
			->with('subdomen')
			->with('rooms')
			// ->where(['>', 'id', 15984])
			// ->offset(3000)
			// ->offset(6000)
			// ->offset(9000)
			// ->offset(12000)
			->offset(15000)
			->limit(3000)
			->all();

		$connection->close();


		$restaurants_location = RestaurantsLocation::find()
			->limit(100000)
			->asArray()
			->all();
		$restaurants_location = ArrayHelper::index($restaurants_location, 'value');

		$restaurants_type = RestaurantsTypes::find()
			->limit(100000)
			->asArray()
			->all();
		$restaurants_type = ArrayHelper::index($restaurants_type, 'value');

		$connection = new \yii\db\Connection([
			'username' => 'root',
			'password' => 'GxU25UseYmeVcsn5Xhzy',
			'charset'  => 'utf8mb4',
			'dsn' => 'mysql:host=localhost;dbname=pmn_bd'
		]);
		$connection->open();
		Yii::$app->set('db', $connection);

		$restaurants_premium = RestaurantsPremium::find()
			->where(['>', 'finish', time()])
			->limit(100000)
			->asArray()
			->all();
		$restaurants_premium = ArrayHelper::index($restaurants_premium, 'gorko_id');

		$connection->close();


		// $spreadsheet = new Spreadsheet();
		// Путь к существующему файлу Excel
		$filePath = '/var/www/pmnetwork_dev/frontend/modules/pmnbd/web/upload/restaurants.xlsx';
		// Загрузка существующего файла
		$spreadsheet = IOFactory::load($filePath);
		foreach ($restaurants as $rest) {
			$premium = isset($restaurants_premium[$rest->gorko_id]);
			if (!$premium) {
				$restaurant_spec_white_list = [9];
				$restaurant_spec_rest = explode(',', $rest->restaurants_spec);
				if (count(array_intersect($restaurant_spec_white_list, $restaurant_spec_rest)) === 0) {
					continue;
				}

				if (!$rest->active) {
					continue;
				}

				if (!$rest->commission) {
					continue;
				}
			}

			$url = '';
			if ($row = (new \yii\db\Query())->select('slug')->from('restaurant_slug')->where(['gorko_id' => $rest->gorko_id])->one()) {
				if ($rest['subdomen']['city_id'] == 4400) {
					$url = 'https://birthday-place.ru/catalog/restoran-' . $row['slug'] . '/';
				} else {
					$url = 'https://' . $rest['subdomen']['alias'] . '.birthday-place.ru/catalog/restoran-' . $row['slug'] . '/';
				}
			}

			$prices = '';
			$capacity = '';
			$room_names = '';
			foreach ($rest['rooms'] as $key => $room) {
				if (($key + 1) == count($rest['rooms'])) {
					$prices .= $key + 1 . ' - ' . $room['price'];
					$capacity .= $key + 1 . ' - ' . $room['capacity'];
					$room_names .= $room['gorko_id'] . ': «' . $room['name'] . '», «' . $rest['name'] . '» на день рождения';
				} else {
					$prices .= $key + 1 . ' - ' . $room['price'] . ' || ';
					$capacity .= $key + 1 . ' - ' . $room['capacity'] . ' || ';
					$room_names .= $room['gorko_id'] . ': «' . $room['name'] . '», «' . $rest['name'] . '» на день рождения || ';
				}
			}

			//Тип помещения
			$types = '';
			$restaurant_types_rest = explode(',', $rest->type);
			foreach ($restaurant_types_rest as $key =>  $types_rest) {
				if (!empty($types_rest)) {
					if (($key + 1) == count($restaurant_types_rest)) {
						$types .= $restaurants_type[$types_rest]['text'];
					} else {
						$types .= $restaurants_type[$types_rest]['text'] . ' || ';
					}
				}
			}

			//Тип локации
			$location = '';
			$restaurant_location_rest = explode(',', $rest->location);
			foreach ($restaurant_location_rest as $key =>  $location_rest) {
				if (!empty($location_rest)) {
					if (($key + 1) == count($restaurant_location_rest)) {
						$location .= $restaurants_location[$location_rest]['text'];
					} else {
						$location .= $restaurants_location[$location_rest]['text'] . ' || ';
					}
				}
			}

			$sheet = $spreadsheet->getSheetByName($rest['subdomen']['name']);

			if (empty($sheet)) {
				$new_sheet = $spreadsheet->createSheet();
				$new_sheet->setTitle($rest['subdomen']['name']);
				$new_sheet = $spreadsheet->getSheetByName($rest['subdomen']['name']);

				// Задание ширины ячейки
				$columnDimension = $new_sheet->getColumnDimension('A');
				$columnDimension->setWidth(25); // Установите необходимую ширину
				$columnDimension = $new_sheet->getColumnDimension('B');
				$columnDimension->setWidth(25); // Установите необходимую ширину
				$columnDimension = $new_sheet->getColumnDimension('C');
				$columnDimension->setWidth(25); // Установите необходимую ширину
				$columnDimension = $new_sheet->getColumnDimension('D');
				$columnDimension->setWidth(12); // Установите необходимую ширину
				$columnDimension = $new_sheet->getColumnDimension('E');
				$columnDimension->setWidth(25); // Установите необходимую ширину
				$columnDimension = $new_sheet->getColumnDimension('F');
				$columnDimension->setWidth(25); // Установите необходимую ширину
				$columnDimension = $new_sheet->getColumnDimension('G');
				$columnDimension->setWidth(50); // Установите необходимую ширину
				$columnDimension = $new_sheet->getColumnDimension('H');
				$columnDimension->setWidth(25); // Установите необходимую ширину
				$columnDimension = $new_sheet->getColumnDimension('I');
				$columnDimension->setWidth(25); // Установите необходимую ширину
				$columnDimension = $new_sheet->getColumnDimension('J');
				$columnDimension->setWidth(25); // Установите необходимую ширину
				$columnDimension = $new_sheet->getColumnDimension('K');
				$columnDimension->setWidth(10); // Установите необходимую ширину
				$columnDimension = $new_sheet->getColumnDimension('L');
				$columnDimension->setWidth(60); // Установите необходимую ширину

				$new_sheet->setCellValue('A1', 'Название реста');
				$new_sheet->setCellValue('B1', 'URL');
				$new_sheet->setCellValue('C1', 'Чек');
				$new_sheet->setCellValue('D1', 'Кол-во залов');
				$new_sheet->setCellValue('E1', 'Вместимость по залам');
				$new_sheet->setCellValue('F1', 'Адрес');
				$new_sheet->setCellValue('G1', 'Кухня');
				$new_sheet->setCellValue('H1', 'Тип помещения');
				$new_sheet->setCellValue('I1', 'Расположение');
				$new_sheet->setCellValue('J1', 'Алкоголь');
				$new_sheet->setCellValue('K1', 'Gorko-ID');
				$new_sheet->setCellValue('L1', 'название залов');

				$new_sheet->setCellValue('A2', $rest['name']);
				$new_sheet->setCellValue('B2', $url);
				$new_sheet->setCellValue('C2', $prices);
				$new_sheet->setCellValue('D2', count($rest['rooms']));
				$new_sheet->setCellValue('E2', $capacity);
				$new_sheet->setCellValue('F2', $rest['address']);
				$new_sheet->setCellValue('G2', $rest['cuisine']);
				$new_sheet->setCellValue('H2', $types);
				$new_sheet->setCellValue('I2', $location);
				$new_sheet->setCellValue('J2', $rest['own_alcohol']);
				$new_sheet->setCellValue('K2', $rest['gorko_id']);
				$new_sheet->setCellValue('L2', $room_names);
			} else {
				$highest_row = $sheet->getHighestRow();

				$sheet->setCellValue('A' . ($highest_row + 1), $rest['name']);
				$sheet->setCellValue('B' . ($highest_row + 1), $url);
				$sheet->setCellValue('C' . ($highest_row + 1), $prices);
				$sheet->setCellValue('D' . ($highest_row + 1), count($rest['rooms']));
				$sheet->setCellValue('E' . ($highest_row + 1), $capacity);
				$sheet->setCellValue('F' . ($highest_row + 1), $rest['address']);
				$sheet->setCellValue('G' . ($highest_row + 1), $rest['cuisine']);
				$sheet->setCellValue('H' . ($highest_row + 1), $types);
				$sheet->setCellValue('I' . ($highest_row + 1), $location);
				$sheet->setCellValue('J' . ($highest_row + 1), $rest['own_alcohol']);
				$sheet->setCellValue('K' . ($highest_row + 1), $rest['gorko_id']);
				$sheet->setCellValue('L' . ($highest_row + 1), $room_names);
			}
		} */

		/* $writer = new Xlsx($spreadsheet);
		// $writer->save('/var/www/pmnetwork_dev/frontend/modules/pmnbd/web/upload/hello_world.xlsx');
		$writer->save($filePath); */

		//** ======== выгрузка рестов по всем городоам в таблицу excel END ========


		/* $connection = new \yii\db\Connection([
			'username' => 'root',
			'password' => 'GxU25UseYmeVcsn5Xhzy',
			'charset'  => 'utf8mb4',
			'dsn' => 'mysql:host=localhost;dbname=pmn'
		]);
		$connection->open();
		Yii::$app->set('db', $connection);

		$restaurant_ya_reviews = YandexReview::find()->all();


		foreach ($restaurant_ya_reviews as $ya_review) {
			$ya_review->save();
		} */





		echo 1111;
	}

	//** ======== обновление отзывов с Яндекс карт ========
	//* (запускать через консольную утилиту /var/www/pmnetwork/console/controllers/GorkoconsoleController.php)
	public function actionYandexReviewUpdate()
	{
		$connection = new \yii\db\Connection([
			'username' => 'pmnetwork',
			'password' => 'P6L19tiZhPtfgseN',
			'charset'  => 'utf8mb4',
			'dsn' => 'mysql:host=localhost;dbname=pmn'
		]);
		$connection->open();
		Yii::$app->set('db', $connection);

		$restaurant_ya_reviews = YandexReview::find()
			->all();
		$connection->close();

		foreach ($restaurant_ya_reviews as $ya_review) {
			$ya_review->save();
		}

		echo ('Обновление отзывов с яндекса завершено!');
		exit;
	}

	public function actionAll()
	{
		$subdomen_model = Subdomen::find()
			->where(['id' => 57])
			->all();

		foreach ($subdomen_model as $key => $subdomen) {
			GorkoApiTest::showAllData([
				[
					'params' => 'city_id=' . $subdomen->city_id . '&type_id=1&event=9',
					'watermark' => '/var/www/pmnetwork/frontend/web/img/ny_ball.png',
					'imageHash' => 'birthdaypmn'
				]
			]);
		}
	}

	public function actionOne()
	{
		$queue_id = Yii::$app->queue->push(new AsyncRenewRestaurants([
			'gorko_id' => 418147,
			'dsn' => Yii::$app->db->dsn,
			'watermark' => '/var/www/pmnetwork/frontend/web/img/ny_ball.png',
			'imageHash' => 'birthdaypmn'
		]));
	}

	public function actionTest()
	{
		GorkoApiTest::showOne([
			[
				'params' => 'city_id=4088&type_id=1&type=30,11,17,14&is_edit=1',
				'watermark' => '/var/www/pmnetwork/frontend/web/img/ny_ball.png'
			]
		]);
	}

	//* проверка поддоменов на нужное количество рестов 
	//* (запускать через консольную утилиту /var/www/pmnetwork/console/controllers/GorkoconsoleController.php)
	public function actionSubdomencheck()
	{
		// SubdomenFilteritem::deactivate();
		$counterActive = 0;
		$counterInactive = 0;
		foreach (Subdomen::find()->all() as $key => $subdomen) {
			// $isActive = Restaurants::find()->where(['city_id' => $subdomen->city_id])->count() > 9;
			// $subdomen->active = $isActive;
			// $subdomen->save();
			if ($subdomen->active) {
				foreach (FilterItems::find()->all() as $filterItem) {
					$hits = $this->getFilterItemsHitsForCity($filterItem, $subdomen->city_id);
					$where = ['subdomen_id' => $subdomen->id, 'filter_items_id' => $filterItem->id];
					$subdomenFilterItem = SubdomenFilteritem::find()->where($where)->one() ?? new SubdomenFilteritem($where);
					$subdomenFilterItem->hits = $hits;
					$subdomenFilterItem->is_valid = 1;
					$subdomenFilterItem->save();
					$hits > 0 ? $counterActive++ : $counterInactive++;
				}
			}
		}
		echo "active=$counterActive; inactive=$counterInactive";
	}

	public function actionRenewelastic()
	{
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		if (ElasticItems::refreshIndex() === true) {
			$this->actionSubdomencheck();
			$this->actionImagePlaceholder();
		}
	}

	private function getFilterItemsHitsForCity($filterItem, $city_id)
	{
		$filter_item_arr = json_decode($filterItem->api_arr, true);
		$main_table = 'restaurants';
		$simple_query = [];
		$nested_query = [];
		$type_query = [];
		$location_query = [];
		$metro_query = [];
		foreach ($filter_item_arr as $filter_data) {

			$filter_query = new FilterQueryConstructorElastic($filter_data, $main_table);

			if ($filter_query->nested) {
				if (!isset($nested_query[$filter_query->query_type])) {
					$nested_query[$filter_query->query_type] = [];
				}
			} elseif ($filter_query->type) {
				if (!isset($type_query[$filter_query->query_type])) {
					$type_query[$filter_query->query_type] = [];
				}
			} elseif ($filter_query->location) {
				if (!isset($location_query[$filter_query->query_type])) {
					$location_query[$filter_query->query_type] = [];
				}
			} elseif ($filter_query->metro) {
				if (!isset($metro_query[$filter_query->query_type])) {
					$metro_query[$filter_query->query_type] = [];
				}
			} else {
				if (!isset($simple_query[$filter_query->query_type])) {
					$simple_query[$filter_query->query_type] = [];
				}
			}

			foreach ($filter_query->query_arr as $filter_value) {
				if ($filter_query->nested) {
					array_push($nested_query[$filter_query->query_type], $filter_value);
				} elseif ($filter_query->type) {
					array_push($type_query[$filter_query->query_type], $filter_value);
				} elseif ($filter_query->location) {
					array_push($location_query[$filter_query->query_type], $filter_value);
				} elseif ($filter_query->metro) {
					array_push($metro_query[$filter_query->query_type], $filter_value);
				} else {
					array_push($simple_query[$filter_query->query_type], $filter_value);
				}
			}
		}
		$final_query = [
			'bool' => [
				'must' => [],
			]
		];
		array_push($final_query['bool']['must'], ['match' => ['restaurant_city_id' => $city_id]]);
		foreach ($simple_query as $type => $arr_filter) {
			$temp_type_arr = [];
			foreach ($arr_filter as $key => $value) {
				array_push($temp_type_arr, $value);
			}
			array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
		}

		foreach ($nested_query as $type => $arr_filter) {
			$temp_type_arr = [];
			foreach ($arr_filter as $key => $value) {
				array_push($temp_type_arr, $value);
			}
			if ($main_table == 'rooms') {
				array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
			} else {
				array_push($final_query['bool']['must'], ['nested' => ["path" => "rooms", "query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
			}
		}

		foreach ($type_query as $type => $arr_filter) {
			$temp_type_arr = [];
			foreach ($arr_filter as $key => $value) {
				array_push($temp_type_arr, $value);
			}
			if ($main_table == 'rooms') {
				array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
			} else {
				array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_types", "query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
			}
		}

		foreach ($location_query as $type => $arr_filter) {
			$temp_type_arr = [];
			foreach ($arr_filter as $key => $value) {
				array_push($temp_type_arr, $value);
			}
			if ($main_table == 'rooms') {
				array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
			} else {
				array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_location", "query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
			}
		}

		foreach ($metro_query as $type => $arr_filter) {
			$temp_type_arr = [];
			foreach ($arr_filter as $key => $value) {
				array_push($temp_type_arr, $value);
			}
			if ($main_table == 'rooms') {
				array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_metro_stations", "query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
			} else {
				array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_metro_stations", "query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
			}
		}

		$final_query = [
			"function_score" => [
				"query" => $final_query,
				"functions" => [
					[
						"filter" => ["match" => ["restaurant_commission" => "2"]],
						"random_score" => [],
						"weight" => 100
					],
				]
			]
		];

		$query = (new ElasticItems())::find()->query($final_query)->limit(0);

		return $query->search()['hits']['total'];
	}

	public function actionImgload()
	{
		//header("Access-Control-Allow-Origin: *");
		$curl = curl_init();
		$file = '/var/www/pmnetwork/pmnetwork_konst/frontend/web/img/favicon.png';
		$mime = mime_content_type($file);
		$info = pathinfo($file);
		$name = $info['basename'];
		$output = curl_file_create($file, $mime, $name);
		$params = [
			//'mediaId' => 55510697,
			'url' => 'https://lh3.googleusercontent.com/XKtdffkbiqLWhJAWeYmDXoRbX51qNGOkr65kMMrvhFAr8QBBEGO__abuA_Fu6hHLWGnWq-9Jvi8QtAGFvsRNwqiC',
			'token' => '4aD9u94jvXsxpDYzjQz0NFMCpvrFQJ1k',
			'watermark' => $output,
			'hash_key' => 'svadbanaprirode'
		];
		curl_setopt($curl, CURLOPT_URL, 'https://api.gorko.ru/api/v2/tools/mediaToSatellite');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_ENCODING, '');
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);


		echo '<pre>';
		$response = curl_exec($curl);

		print_r(json_decode($response));
		curl_close($curl);

		//echo '<pre>';

		//echo '<pre>';
	}

	private function sendMail($to, $subj, $msg)
	{
		$message = Yii::$app->mailer->compose()
			->setFrom(['svadbanaprirode@yandex.ru' => 'Свадьба на природе'])
			->setTo($to)
			->setSubject($subj)
			//->setTextBody('Plain text content')
			->setHtmlBody($msg);
		//echo '<pre>';
		//print_r($message);
		//exit;
		if (count($_FILES) > 0) {
			foreach ($_FILES['files']['tmp_name'] as $k => $v) {
				$message->attach($v, ['fileName' => $_FILES['files']['name'][$k]]);
			}
		}
		return $message->send();
	}

	public function actionImagePlaceholder()
	{
		foreach (Rooms::find()->where(['like', 'cover_url', 'no_photo'])->all() as $room) {
			$room->cover_url = '/img/bd/no_photo_s.png';
			$room->save();
		}
	}

	public function actionUpdate()
	{
		foreach (Images::find()->where(['type' => 'rooms'])->all() as $image) {
			# code...
		}
	}

	public function actionRefreshMetroList()
	// /test/refresh-metro-list/
	{
		//** ======== Обновление списка станций метро START ======== 
		$connection = new \yii\db\Connection([
			'username' => 'pmnetwork',
			'password' => 'P6L19tiZhPtfgseN',
			'charset'  => 'utf8mb4',
			'dsn' => 'mysql:host=localhost;dbname=pmn'
		]);
		$connection->open();
		Yii::$app->set('db', $connection);

		$restaurants = Restaurants::find()
			->limit(100000)
			->all();

		$metros_cities = [];

		//собираем все метро и соотвествующие им города из общей БД
		foreach ($restaurants as $key => $rest) {
			if (isset($rest['metro_station_id']) && !empty($rest['metro_station_id'])) {
				if (strpos($rest['metro_station_id'], ',') !== false) {
					$metro_stations_id = explode(',', $rest['metro_station_id']);

					foreach ($metro_stations_id as $key => $station_id) {
						$metros_cities[$station_id] = $rest['city_id'];
					}
				} else {
					$metros_cities[$rest['metro_station_id']] = $rest['city_id'];
				}
			}
		}

		$connection = new \yii\db\Connection([
			'username' => 'pmnetwork',
			'password' => 'P6L19tiZhPtfgseN',
			'charset'  => 'utf8mb4',
			'dsn' => 'mysql:host=localhost;dbname=pmn_bd'
		]);
		$connection->open();
		Yii::$app->set('db', $connection);

		foreach ($metros_cities as $metro_table_id => $city_id) {
			$metro_station = MetroStations::find()
				->where(['table_id' => $metro_table_id])
				->one();

			//находим все метро с таким же именем
			$metro_with_same_name = MetroStations::find()
				->where(['alias' => $metro_station->alias])
				->all();

			//оставляем станции только нужного города
			$metro_same_name_and_city = [];
			foreach ($metro_with_same_name as $metro) {
				if (isset($metros_cities[$metro['table_id']]) && $metros_cities[$metro['table_id']] == $city_id) {
					$metro_same_name_and_city[] = $metro;
				}
			}

			//собираем id одинаковых станций метро
			$same_station_table_id = '';
			foreach ($metro_same_name_and_city as $key => $metro) {
				if ($key == 0) {
					$same_station_table_id = strval($metro['table_id']);
				} else {
					$same_station_table_id .= ',' . $metro['table_id'];
				}
			}

			//добавляем метро в локальную таблицу БД
			if (Metros::find()->where(['city_id' => $city_id])->andWhere(['alias' => $metro_station->alias])->exists()) {
				$model = Metros::find()->where(['city_id' => $city_id])->andWhere(['alias' => $metro_station->alias])->one();
			} else {
				$model = new Metros();
			}

			$model->table_id = $metro_station->table_id;
			$model->same_station_table_id = $same_station_table_id;
			$model->city_id = $city_id;
			$model->line_id = $metro_station->line_id;
			$model->name = $metro_station->name;
			$model->latitude = $metro_station->latitude;
			$model->longitude = $metro_station->longitude;
			$model->alias = $metro_station->alias;
			$model->save();
		}
		//** ======== Обновление списка станций метро END ======== 

		echo 'Обновление списка станций метро завершено';
		exit;
	}

	public function actionRefreshMetroFilterItems()
	// /test/refresh-metro-filter-items/
	{
		$metro_stations = Metros::find()->all();

		foreach ($metro_stations as $metro) {
			$metro_stations_id = [];
			if (strpos($metro['same_station_table_id'], ',') !== false) {
				$metro_stations_id_arr = explode(',', $metro['same_station_table_id']);

				foreach ($metro_stations_id_arr as $station_id) {
					$metro_stations_id[] = $station_id;
				}
			} else {
				$metro_stations_id[] = $metro['same_station_table_id'];
			}

			if (FilterItems::find()->where(['value' => $metro->table_id])->andWhere(['filter_id' => 12])->exists()) {
				$filterItem = FilterItems::find()->where(['value' => $metro->table_id])->andWhere(['filter_id' => 12])->one();

				$filterItem->api_arr = json_encode(["0" => ["table" => "restaurants", "key" => "metro_stations.id", "value" => $metro_stations_id]], JSON_FORCE_OBJECT);
				$filterItem->save(false);
			} else {
				$filterItem = new FilterItems();
				$filterItem->filter_id = 12;
				$filterItem->value = $metro->table_id;
				$filterItem->text = $metro->name;
				$filterItem->api_arr = json_encode(["0" => ["table" => "restaurants", "key" => "metro_stations.id", "value" => $metro_stations_id]], JSON_FORCE_OBJECT);
				$filterItem->active = 1;
				$filterItem->save(false);
			}

			if (!Slices::find()->where(['type' => 'metro', 'alias' => 'metro-' . $metro->alias, 'description' => $metro->city_id])->exists()) {
				$slice = new Slices();
				$slice->type = 'metro';
				$slice->alias = 'metro-' . $metro->alias;
				$slice->description = $metro->city_id;
				$slice->params = '{"metro":"' . $metro->table_id . '"}';
				$slice->save(false);
			}

			if (!Pages::find()->where(['type' => 'metro-' . $metro->alias])->exists()) {
				$page = new Pages();
				$page->name = $metro->name;
				$page->type = 'metro-' . $metro->alias;
				$page->save();
			} else {
				$currentPage = Pages::find()->where(['type' => 'metro-' . $metro->alias])->one();

				if (SiteObject::find()->where(['row_id' => $currentPage->id])->andWhere(['table_name' => 'pages'])->exists()) {
					$currentSiteObject = SiteObject::find()->where(['row_id' => $currentPage->id])->andWhere(['table_name' => 'pages'])->one();
					$currentSiteObjectSeo = SiteObjectSeo::find()->where(['site_object_id' => $currentSiteObject->id])->one();
					//   титл: "Отметить день рождения у метро {название метро}: кол-во залов".
					$currentSiteObjectSeo->title = 'Отметить день рождения у метро ' . $metro->name . ': **count_dec=ресторан**';
					//   Дескрипшн: Список ресторанов и кафе у метро {название метро} в Москве, где вы можете отлично отметить ваш день рождения: ☆ кол-во залов ☆ с подробной информацие".
					$currentSiteObjectSeo->description = 'Список ресторанов и кафе у метро ' . $metro->name . ' в **city_dec**, где вы можете отлично отметить ваш день рождения: ☆ **count_dec=ресторан** ☆ с подробной информацией';
					//   Х1: "День рождения у метро {название метро}"
					$currentSiteObjectSeo->heading = 'День рождения у метро ' . $metro->name;
					$currentSiteObjectSeo->save();
				} else {
					$currentPage::createSiteObjects();
				}
			}
		}


		echo 'Таблицы filter_items, slices, pages успешно обновлены по таблице metros';
		exit;
	}

	//* Обновление количества ресторанов на срезах "метро" в зависимости от города 
	public function actionRefreshNumberOfRestaurantInMetroSlices()
	// /test/refresh-number-of-restaurant-in-metro-slices/
	{
		$filter_model = Yii::$app->params['filter_model'];
		$slices_model = Slices::find()->where(['type' => 'metro'])->all();
		$elastic_model = new ElasticItems;
		$subdomens = Subdomen::find()->all();

		foreach ($subdomens as $subdomen) {
			//задаем параметр id города для выбора количества ресторанов в этом городе
			Yii::$app->params['subdomen_id'] = $subdomen->city_id;

			foreach ($slices_model as $slice) {
				//проверяем, что метро находится в этом городе
				if ($slice->description == $subdomen->city_id) {
					$metro_table_id = json_decode($slice->params, true)['metro'];
					$slice_obj = new QueryFromSlice($slice->alias, true);
					$params = UpdateFilterItems::parseGetQuery($slice_obj->params, $filter_model, $slices_model);
					$items = new ItemsFilterElastic($params['params_filter'], 1, 1, false, 'restaurants', $elastic_model);

					if (SlicesMetroExtra::find()->where(['slices_id' => $slice->id])->exists()) {
						$totalItem = SlicesMetroExtra::find()->where(['slices_id' => $slice->id])->one();
					} else {
						$totalItem = new SlicesMetroExtra();
						$totalItem->slices_id = $slice->id;
					}

					$totalItem->metro_table_id = $metro_table_id;
					$totalItem->restaurant_count = $items->total;
					$totalItem->save();
				}
			}
		}

		echo 'Количество ресторанов у всех срезов метро обновлено';
		exit;
	}

	public function actionRefreshActiveMetroFilterItems()
	// /test/refresh-active-metro-filter-items
	{
		$slices_metro = SlicesMetroExtra::find()->all();

		foreach ($slices_metro as $slice) {
			$filter_item = FilterItems::find()->where(['filter_id' => 12, 'value' => $slice->metro_table_id])->one();

			if ($slice->restaurant_count > 0) {
				$filter_item->active = 1;
			} elseif ($slice->restaurant_count === 0) {
				$filter_item->active = 0;
			}
			$filter_item->save();
		}

		echo 'Срезы без ресторанов деактивированы';
		exit;
	}
}
