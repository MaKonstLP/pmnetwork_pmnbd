<?php
namespace app\modules\pmnbd\controllers;

use Yii;
use common\models\GorkoApiTest;
use common\models\Subdomen;
use common\models\Restaurants;
use common\models\RestaurantsYandex;
use common\models\RestaurantsModule;
use frontend\modules\pmnbd\models\ElasticItems;
use yii\web\Controller;
use common\components\AsyncRenewRestaurants;
use common\models\FilterItems;
use common\models\siteobject\SiteObject;
use common\models\siteobject\SiteObjectSeo;
use common\models\elastic\FilterQueryConstructor;
use common\models\elastic\FilterQueryConstructorElastic;
use common\models\Images;
use common\models\Rooms;
use frontend\modules\pmnbd\models\SubdomenFilteritem;
use yii\helpers\ArrayHelper;

class TestController extends BaseFrontendController
{
	public function actionSendmessange()
	{
		$to = ['zadrotstvo@gmail.com'];
		$subj = "Тестовая заявка";
		$msg = "Тестовая заявка";
		$message = $this->sendMail($to,$subj,$msg);
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

		$connection = new \yii\db\Connection([
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
		exit;

		echo 1111;
	}

	public function actionAll()
	{
		$subdomen_model = Subdomen::find()
			->where(['id' => 57])
			->all();

		foreach ($subdomen_model as $key => $subdomen) {
			GorkoApiTest::showAllData([
				[
					'params' => 'city_id='.$subdomen->city_id.'&type_id=1&event=9',
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

	public function actionSubdomencheck()
	{
		SubdomenFilteritem::deactivate();
		$counterActive = 0;
		$counterInactive = 0;
		foreach (Subdomen::find()->all() as $key => $subdomen) {
			$isActive = Restaurants::find()->where(['city_id' => $subdomen->city_id])->count() > 9;
			$subdomen->active = $isActive;
			$subdomen->save();
			if($subdomen->active) {
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
		if(ElasticItems::refreshIndex() === true) {
			$this->actionSubdomencheck();
			$this->actionImagePlaceholder();
		}
	}

	private function getFilterItemsHitsForCity($filterItem, $city_id) {
		$filter_item_arr = json_decode($filterItem->api_arr, true);
		$main_table = 'restaurants';
		$simple_query = [];
		$nested_query = [];
		$type_query = [];
		$location_query = [];
		foreach ($filter_item_arr as $filter_data) {

			$filter_query = new FilterQueryConstructorElastic($filter_data, $main_table);

			if($filter_query->nested){
				if(!isset($nested_query[$filter_query->query_type])){
					$nested_query[$filter_query->query_type] = [];
				}
			}
			elseif($filter_query->type){
				if(!isset($type_query[$filter_query->query_type])){
					$type_query[$filter_query->query_type] = [];
				}
			}
			elseif($filter_query->location){
				if(!isset($location_query[$filter_query->query_type])){
					$location_query[$filter_query->query_type] = [];
				}
			}
			else{
				if(!isset($simple_query[$filter_query->query_type])){
					$simple_query[$filter_query->query_type] = [];
				}
			}

			foreach ($filter_query->query_arr as $filter_value) {
				if($filter_query->nested){
					array_push($nested_query[$filter_query->query_type], $filter_value);
				}
				elseif($filter_query->type){
					array_push($type_query[$filter_query->query_type], $filter_value);
				}
				elseif($filter_query->location){
					array_push($location_query[$filter_query->query_type], $filter_value);
				}
				else{
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
			if($main_table == 'rooms'){
				array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
			}
			else{
				array_push($final_query['bool']['must'], ['nested' => ["path" => "rooms","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
			}
		}

		foreach ($type_query as $type => $arr_filter) {
			$temp_type_arr = [];
			foreach ($arr_filter as $key => $value) {
				array_push($temp_type_arr, $value);
			}
			if($main_table == 'rooms'){
				array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
			}
			else{
				array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_types","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
			}
		}

		foreach ($location_query as $type => $arr_filter) {
			$temp_type_arr = [];
			foreach ($arr_filter as $key => $value) {
				array_push($temp_type_arr, $value);
			}
			if($main_table == 'rooms'){
				array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
			}
			else{
				array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_location","query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
			}
		}
		$final_query = [
			"function_score" => [
		      "query" => $final_query,
		      "functions" => [
	              [
	                  "filter" => [ "match" => [ "restaurant_commission" => "2" ] ],
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
			'url'=>'https://lh3.googleusercontent.com/XKtdffkbiqLWhJAWeYmDXoRbX51qNGOkr65kMMrvhFAr8QBBEGO__abuA_Fu6hHLWGnWq-9Jvi8QtAGFvsRNwqiC',
			'token'=> '4aD9u94jvXsxpDYzjQz0NFMCpvrFQJ1k',
			'watermark' => $output,
			'hash_key' => 'svadbanaprirode'
		];
		curl_setopt($curl, CURLOPT_URL, 'https://api.gorko.ru/api/v2/tools/mediaToSatellite');
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
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

	private function sendMail($to,$subj,$msg) {
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
}