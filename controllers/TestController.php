<?php
namespace app\modules\pmnbd\controllers;

use Yii;
use common\models\GorkoApiTest;
use yii\web\Controller;
use app\modules\pmnbd\models\ElasticItems;

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

	public function actionRenewelastic()
	{
		ElasticItems::refreshIndex();
	}

	public function actionImgload()
	{
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
	}
}