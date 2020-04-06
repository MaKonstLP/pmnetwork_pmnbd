<?php
namespace app\modules\pmnbd\controllers;

use Yii;
use common\models\GorkoApiTest;
use yii\web\Controller;

class TestController extends Controller
{
	public function actionIndex()
	{
		GorkoApiTest::renewAllData([
			'city_id=4400&fields=params'
		]);
	}

	public function actionTest()
	{
		GorkoApiTest::showOne([
			'city_id=4400&fields=params'
		]);
	}

	public function actionImgload()
	{
		header("Access-Control-Allow-Origin: *");
		$curl = curl_init();
		$params = [
			'mediaId' => 1,
			'url'=>'https://lh3.googleusercontent.com/XKtdffkbiqLWhJAWeYmDXoRbX51qNGOkr65kMMrvhFAr8QBBEGO__abuA_Fu6hHLWGnWq-9Jvi8QtAGFvsRNwqiC',
			'token'=> '4aD9u94jvXsxpDYzjQz0NFMCpvrFQJ1k'
		];
		curl_setopt($curl, CURLOPT_URL, 'https://api.gorko.ru/api/v2/tools/mediaToSatellite');
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	    curl_setopt($curl, CURLOPT_ENCODING, '');
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

	    

	    $response = curl_exec($curl);
	    curl_close($curl);

	    //echo '<pre>';
	    print_r($response);
	    //echo '<pre>';

		


	    
	}
}